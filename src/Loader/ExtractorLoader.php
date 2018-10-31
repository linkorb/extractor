<?php

namespace Extractor\Loader;

use Extractor\Model\Extractor;
use Extractor\Model\InputDefinition;
use Extractor\Model\Command;

class ExtractorLoader
{
    public function load(array $config)
    {
        $e = new Extractor();
        $e->setName($config['name'] ?? null);
        $e->setDescription($config['description'] ?? null);

        foreach ($config['inputs'] ?? [] as $name=>$details) {
            $i = new InputDefinition();
            $i->setName($name);
            $i->setType($details['type'] ?? null);
            $i->setRequired($details['required'] ?? null);
            $e->addInputDefinition($i);
        }

        foreach ($config['commands'] ?? [] as $name=>$details) {
            $command = new Command();
            $command->setName($name);
            $command->setConnectionName($details['connection'] ?? null);
            $command->setMethodName($details['method'] ?? null);
            $command->setCommand($details['command'] ?? []);
            $command->setArguments($details['arguments'] ?? []);
            $e->addCommand($command);
        }
        return $e;
    }
}
