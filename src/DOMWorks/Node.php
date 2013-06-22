<?php namespace DOMWorks;

class Node
{
    /**
     * Global context
     * @var DOMNode
     */
    private static $_context;

    /**
     * ???
     * @var array
     */
    private static $identificators = array();

    /**
     * Node instance
     * @var DOMNode
     */
    public $node;

    /**
     * Context instance
     * @var DOMNode
     */
    public $context;

    /**
     * Document instance
     * @var DOMDocument
     */
    private $document;

    /**
     * Internal DOMWorks reference
     * @var DOMWorks
     */
    private $domworks;

    public function __construct($element, $value, DOMWorks $domworks)
    {
        // cache document
        $this->document = $document->document;

        // cache domworks
        $this->domworks = $domworks;

        // create element
        $this->node = $this->document->createElement($element);

        // if global context is set, set local context from it
        if (self::$_context) $this->context = self::$_context;

        // set value
        if (!empty($value)) $this->setValue($value);
    }

    /**
     * Sets the element value
     * @param string $value
     */
    public function setValue($value)
    {
        $text = $this->document->createTextNode($value);
        $this->node->appendChild($text);

        return $this;
    }

    public function append($append)
    {
        // in case append is a closure, we set the content and let it live on it's own
        if ($append instanceof Closure)
        {
            self::$_context = $this->node;
            call_user_func($append, $this->domworks, $this);
            self::$_context = null;
        }

        // in case append is DOMNode or Node
        else if ($append instanceof \DOMNode || $append instanceof self)
        {
            if ($append instanceof $this) $append = $append->node;
            $this->node->appendChild($append);
        }

        return $this;
    }

    /**
     * Attempt to add attribute
     * @param  string $attribute
     * @param  string $value
     * @return self
     */
    public function __call($attribute, $value)
    {
        $this->attr($attribute, array_pop($value));
        return $this;
    }

    /**
     * Actual attribute addition
     * @param  string $attribute
     * @param  string $value
     * @return self
     */
    public function attr($attribute, $value = null)
    {
        // if attribute is a list of attributes, and no value is given
        if (is_array($attribute) && is_null($value))
        {
            // add each attribute
            foreach ($attribute as $attr => $val)
            {
                $this->attr($attr, $val);
            }
        }

        // otherwise, just add the attribute
        else
        {
            $this->node->setAttribute($attribute, $value);
        }

        return $this;
    }

    public function href($url)
    {
        $tag = strtoupper($this->node->tagName);

        if ($tag === 'LINK' || $tag === 'A')
        {
            $this->attr(__FUNCTION__, $url);
        }

        return $this;
    }

    public function id($id)
    {
        if (!in_array($id, self::$identificators))
        {
            $this->attr(__FUNCTION__, $id);
            self::$identificators[] = $id;
        }

        return $this;
    }

    /*
     * Extend with CSS object
     * ----------------------
     * [ ] — Append to <style /> tag with classes / id's etc.
     */
    public function css($attribute, $value = null)
    {
        $style = '';
        if (is_array($attribute) && is_null($value))
        {
            foreach ($attribute as $property => $val)
            {
                $style .= sprintf('%s: %s; ', $property, $val);
            }
        }
        else
        {
            $style .= sprintf('%s: %s; ', $attribute, $value);
        }

        $this->style($style);

        return $this;
    }

    /*
     * Extend with JS object
     * ---------------------
     * [ ] — Append to <script /> tag with DOM Events etc.
     * [ ] — Compile PHP functions to JavaScript (CSS manipulations and stuff)
     * ---------------------
     * Opera has some problems with current method...
     */
    public function js($event, array $css, $reset = true)
    {
        $mirror = array(
            'mouseover' => 'mouseout',
            'mouseenter' => 'mouseleave',
            'focus' => 'blur'
        );

        $reset = array_key_exists($event, $mirror);

        if ($reset)
        {
            $style = explode(';', $this->node->getAttribute('style'));
            $preserved = array();

            foreach ($style as $va)
            {
                $va = trim($va);
                if(empty($va)) continue;

                $stuff = explode(':', $va);
                $preserved[trim($stuff[0])] = trim($stuff[1]);
            }
        }

        $on = array();
        $on[] = '(function(a){';
        foreach ($css as $property => $value)
        {
            $on[] = "a.style['{$property}'] = '{$value}';";
        }
        $on[] = '})(this);';

        $this->{'on' . $event}(implode(' ', $on));

        if ($reset)
        {
            $off = array();
            $off[] = $on[0];

            foreach ($css as $property => $value)
            {
                if (array_key_exists($property, $preserved))
                {
                    $off[] = "a.style['{$property}'] = '{$preserved[$property]}'; ";
                }
                else
                {
                    $off[] = "a.style['{$property}'] = ''; ";
                }
            }

            $off[] = $on[sizeof($on) - 1];

            $this->{'on' . $mirror[$event]}(implode(' ', $off));
        }

        return $this;
    }
}