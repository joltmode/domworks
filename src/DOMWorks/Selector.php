<?php namespace DOMWorks;

use DOMDocument;
use DOMXPath;
use Zend\Dom\Query;
use DOMWorks\NodeList;
use DOMWorks\Element;
use Zend\Stdlib\ErrorHandler;

class Selector extends Query
{
    protected $document;
    protected $context;

    public function __construct(DOMDocument $document, NodeList $context = null)
    {
        $this->document = $document;
        $this->context = $context;
    }

    public function queryXpath($xquery, $query = null)
    {
        $nodeList = $this->getNodeList($this->document, $xquery);
        return $nodeList;
    }

    /**
     * Prepare node list
     *
     * @param  DOMDocument $document
     * @param  string|array $xpathQuery
     * @return array
     */
    protected function getNodeList($document, $xpathQuery)
    {
        $xpath = new DOMXPath($document);
        foreach ($this->xpathNamespaces as $prefix => $namespaceUri) {
            $xpath->registerNamespace($prefix, $namespaceUri);
        }
        if ($this->xpathPhpFunctions) {
            $xpath->registerNamespace("php", "http://php.net/xpath");
            ($this->xpathPhpFunctions === true) ?
                $xpath->registerPHPFunctions()
                : $xpath->registerPHPFunctions($this->xpathPhpFunctions);
        }
        $xpathQuery = (string) $xpathQuery;

        ErrorHandler::start();

        $elements = new NodeList($document);

        if (count($this->context) === 0)
        {
            //var_dump($xpathQuery);
            $elements->add($xpath->query($xpathQuery));
        }
        else
        {
            foreach ($this->context as $context)
            {
                //var_dump('.' . $xpathQuery);
                $elements->add($xpath->query('.' . $xpathQuery, $context->element));
            }
        }

        $error = ErrorHandler::stop();

        if ($error)
        {
            throw $error;
        }

        return $elements;
    }
}