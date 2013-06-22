<?php namespace DOMWorks;

class DOMWorks
{
    /**
     * Document instance
     * @var DOMDocument
     */
    private $document;

    /**
     * HTML / document element instance
     * @var DOMNode
     */
    public $html;

    /**
     * Head element instance
     * @var DOMNode
     */
    public $head;

    /**
     * Body element instance
     * @var DOMNode
     */
    public $body;

    /**
     * Is the document saved?
     * @var boolean
     */
    private $saved = false;

    /**
     * Document creation
     *
     * Creates a document
     * @return void
     */
    public function __construct()
    {
        $i = new \DOMImplementation;

        // HTML5
        $doctype = $i->createDocumentType('html');

        $document = $i->createDocument('', 'html', $doctype);

        $document->formatOutput = true;

        // store document
        $this->document = $document;

        // store HTML
        $this->html = $document->documentElement;
    }

    /**
     * Creates HTML essential elements
     * @param string $title Title of the document
     */
    public function createEssentials($title)
    {
        // add head
        $this->head = $this->document->createElement('head');
        $this->html->appendChild($this->head);

        // add utf-8 meta
        $utf8 = $this->document->createElement('meta');
        $utf8->setAttribute('charset', 'utf-8');
        $this->head->appendChild($utf8);

        // add title
        $title = $this->document->createTextNode($title);
        $titleElement = $this->document->createElement('title');
        $titleElement->appendChild($title);
        $this->head->appendChild($titleElement);

        // add body
        $this->body = $this->document->createElement('body');
        $this->html->appendChild($this->body);
    }

    /**
     * Element creation shorthand
     * @param string $tag tagName
     * @param string $value Nodes' content
     * @param DOMElement|DOMWorks\Element $context
     */
    public function __invoke($tag, $value = '', $context = null)
    {
        // check if two params passed, and second looks like context
        if ($value instanceof \DOMNode || $value instanceof Node)
        {
            $context = $value;
            $value = '';
        }

        // in case context is given, but is neither DOMNode or DOMWorks\Node
        if ($context !== null && !($context instanceof \DOMNode || $context instanceof Node))
        {
            // kill
            throw new InvalidArgumentException('Context must be either an instance of DOMNode or DOMWorks\Node.');
        }

        // in case context element is DOMWorks\Node
        else if ($conext !== null && $context instanceof Node)
        {
            // take DOMNode
            $context = $context->node;
        }

        // create new Node
        $node = new Node($tag, $value, $this);

        // if no context given, look up nodes context, if no node context, take body
        $context = $context ?: $node->context ?: $this->body;

        $context->appendChild($node->node);

        return $node;
    }

    /**
     * Render the document
     * @return string
     */
    public function __toString()
    {
        return preg_replace('/<\?xml.+\?>\s+/', '', $this->document->saveXML());
    }
}