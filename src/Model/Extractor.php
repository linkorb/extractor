<?php

namespace Extractor\Model;

use Extractor\Connection\ConnectionInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
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
        $res = [];
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

        $res['inputs'] = [0 => $inputs];
        $expressionLanguage = new ExpressionLanguage();

        foreach ($this->commands as $name => $command) {
            $connectionName = $command->getConnectionName() ?? null;
            if (!$connectionName) {
                throw new RuntimeException("No connection specified for command " . $name);
            }
            if (!isset($this->connections[$connectionName])) {
                throw new RuntimeException("Unknown connection specified for command " . $name . ' (' . $connectionName . ')');
            }
            $connection = $this->connections[$connectionName];
            $methodName = $command->getMethodName();
            if (!$methodName) {
                throw new RuntimeException("No method specified for command " . $name);
            }

            $arguments = [];
            foreach ($command->getArguments() as $key => $value) {
                // echo "Converting $key=$value\n";
                // print_r($res);
                $arguments[$key] = $expressionLanguage->evaluate($value, $res);
            }
            // print_r($arguments);
            $rows = $connection->$methodName($command->getCommand(), $arguments);
            $res[$name] = $rows;
        }

        return $res;
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
