<?php namespace DOMWorks;

use DOMDocument;
use DOMWorks\Element;
use DOMWorks\Selector;

class DOMWorks
{
    private $document;
    private $select;

    public function __construct(DOMDocument $document)
    {
        $this->document = $document;

        $this->select = new Selector($document);
    }

    public function __invoke($selector)
    {
        return $this->select->execute($selector);
    }

    public function __toString()
    {
        return $this->document->saveHTML();
    }

    public static function loadHTMLFile($file)
    {
        $document = new DOMDocument;
        $document->loadHTMLFile($file);

        return new static($document);
    }
}