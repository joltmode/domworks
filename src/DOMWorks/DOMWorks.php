<?php namespace DOMWorks;

use DOMDocument;
use DOMWorks\Element;
use DOMWorks\Selector;

class DOMWorks
{
    private $document;
    private $select;

    public function __construct(DOMDocument $document, Element $context = null)
    {
        $this->document = $document;
        $this->context = $context;

        $this->select = new Selector($document);
    }

    public function __invoke($selector)
    {
        return $this->select->execute($selector);
    }

    public function __toString()
    {
        return $this->document->saveHTML($this->context);
    }

    public static function loadHTMLFile($file)
    {
        $document = new DOMDocument;
        $document->loadHTMLFile($file);

        return new static($document);
    }
}