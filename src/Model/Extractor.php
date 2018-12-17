<?php

namespace Extractor\Model;

use Extractor\Connection\ConnectionInterface;
use RuntimeException;

class Extractor extends AbstractModel
{
    protected $name;
    protected $description;
    protected $connections;
    protected $inputDefinitions = [];
    protected $commands = [];

    public function extract(array $connections, array $inputs)
    {
        $context = new Context();
        // check if all passed inputs have a definition
        foreach ($inputs as $key=>$value) {
            if (!isset($this->inputDefinitions[$key])) {
                throw new RuntimeException("Passing undefined input argument " . $key);
            }
        }

        // Check if all defined inputs are in the input array
        foreach ($this->inputDefinitions as $inputDefinition) {
            if (!isset($inputs[$inputDefinition->getName()])) {
                throw new RuntimeException("Missing required input argument " . $inputDefinition->getName());
            }
        }

        $this->connections = $connections;

        $inputFrame = new Frame('inputs');
        $inputFrame->setData([0 => $inputs]);
        $context->addFrame($inputFrame);

        foreach ($this->commands as $name => $command) {
            // echo "COMMAND: $name\n";
            $connectionName = $command->getConnectionName() ?? 'default';
            if (!isset($this->connections[$connectionName])) {
                throw new RuntimeException("Unknown connection specified for command " . $name . ' (' . $connectionName . ')');
            }
            $connection = $this->connections[$connectionName];
            $methodName = $command->getMethodName();
            if (!$methodName) {
                throw new RuntimeException("No method specified for command " . $name);
            }

            $arguments = [];
            $variables = $context->toArray();
            $xml = $context->toXml();
            // echo $context->toXmlString();
            foreach ($command->getArguments() as $key => $xpath) {
                // echo "Resolving argument $key=$xpath\n";
                $elements = $xml->xpath($xpath);
                if (count($elements)==0) {
                    throw new RuntimeException("XPath expression resolved to 0 elements: " . $xpath);
                }
                if (count($elements)>1) {
                    throw new RuntimeException("XPath expression resolved to more than one element: " . $xpath);
                }
                $result = (string)$elements[0];
                $arguments[$key] = $result;
            }
            // echo $command->getCommand() . PHP_EOL;
            // print_r($arguments);
            $rows = $connection->$methodName($command->getCommand(), $arguments);
            $frame = new Frame($name, $rows);
            $context->addFrame($frame);
        }

        return $context;
    }

    public function addInputDefinition(InputDefinition $input)
    {
        $this->inputDefinitions[$input->getName()] = $input;
    }

    public function addCommand(Command $command)
    {
        $this->commands[$command->getName()] = $command;
    }
}
