<?php

namespace Extractor\Model;

use Extractor\Connection\ConnectionInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use RuntimeException;
use SimpleXMLElement;

class Context extends AbstractModel
{
    protected $name;
    private $frames = [];

    public function getFrames()
    {
        return $this->frames;
    }

    public function hasFrame($name)
    {
        return isset($this->frames[$name]);
    }

    public function getFrame($name)
    {
        if (!$this->hasFrame($name)) {
            throw new RuntimeException("No such frame in frame: " . $name);
        }
        return $this->frames[$name];
    }

    public function addFrame(frame $frame)
    {
        $this->frames[$frame->getName()] = $frame;
    }

    public function toArray()
    {
        $data = [];
        foreach ($this->frames as $frame) {
            $data[$frame->getName()] = $frame->getData();
        }
        return $data;
    }

    public function toXml()
    {
        $xml = new SimpleXMLElement("<context></context>");
        foreach ($this->frames as $frame) {
            foreach ($frame->getData() as $key => $values) {
                $e = $xml->addChild($frame->getName());
                foreach ($values as $k=>$v) {
                    $e->addAttribute($k, $v);
                }

            }
        }
        return $xml;
    }

    public function toXmlString()
    {
        $dom = dom_import_simplexml($this->toXml())->ownerDocument;
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

}
