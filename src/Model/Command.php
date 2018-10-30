<?php

namespace Extractor\Model;

class Command extends AbstractModel
{
    protected $name;
    protected $connectionName;
    protected $methodName;
    protected $command;
    protected $arguments = [];
}
