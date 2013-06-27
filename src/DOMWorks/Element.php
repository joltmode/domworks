<?php namespace DOMWorks;

use Closure;
use DOMElement;

class Element
{
    public $element;
    public $ownerDocument;

    public function __construct(DOMElement $element)
    {
        $this->element = $element;
        $this->ownerDocument = $element->ownerDocument;
    }

    public function href($value = null)
    {
        $name = strtolower($this->element->tagName);
        if ($name === 'a' || $name === 'link')
        {
            $this->href($value);
        }
    }

    /**
     * Get attribute dynamically
     * @param  string $attribute
     * @return string
     */
    public function __get($attribute)
    {
        return $this->element->getAttribute($attribute);
    }

    /**
     * Set attribute dynamically
     * @param string $attibute
     * @param Closure|scalar $value
     */
    public function __set($attribute, $value)
    {
        // we have attempted to set the value by a closure
        if ($value instanceof Closure)
        {
            // call closure and set
            $value = call_user_func($value, $this);
        }

        $this->element->setAttribute($attribute, $value);
    }

    /**
     * Dynamic getter and setter shortcut
     * @param  string $attribute
     * @param  string $value
     * @return self
     */
    public function __call($attribute, $arguments)
    {
        if (count($arguments) == 1 && is_array($arguments[0]))
        {
            $arguments = $arguments[0];
        }

        // a parameter has been given, assume a setter
        if (count($arguments) > 0 && !empty($arguments[0]))
        {
            $this->{$attribute} = $arguments[0];
        }

        // otherwise, we're getting
        else
        {
            return $this->{$attribute};
        }

        return $this;
    }
}