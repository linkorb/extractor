<?php

namespace Extractor\Model;

use Extractor\Connection\ConnectionInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use RuntimeException;

class Frame extends AbstractModel
{
    private $name;
    private $data = [];

    public function __construct(string $name, array $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }
}
