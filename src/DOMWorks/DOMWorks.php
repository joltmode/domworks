<?php namespace DOMWorks;

use DOMDocument;
use DOMXPath;
use DOMWorks\NodeList;
use Symfony\Component\CssSelector\CssSelector;


class DOMWorks
{
    public $document;
    private $select;

    private $xpath;

    public function __construct(DOMDocument $document)
    {
        $this->document = $document;
        $this->xpath = new DOMXPath($this->document);

        //$this->select = new Selector($document);
    }

    public function __invoke($selector, $context = null)
    {
        return $this->find($selector, $context);
    }

    public function find($selector, $context = null)
    {
        // var_dump(__METHOD__);

        $selector = CssSelector::toXPath($selector);
        $elements = new NodeList($this);

        if (!$context || count($context) === 0)
        {
            // var_dump($selector);
            $elements->add($this->xpath->query($selector));
        }
        else
        {
            foreach ($context as $c)
            {
                // var_dump($selector);
                $elements->add($this->xpath->query($selector, $c->element));
            }
        }

        return $elements;
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