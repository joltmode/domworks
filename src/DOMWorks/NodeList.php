<?php namespace DOMWorks;

use DOMNodeList;
use DOMDocument;
use DOMWorks\Element;
use DOMElement;
use BadMethodCallException;
use Iterator, Countable, ArrayAccess;
use DOMWorks\Selector;

class NodeList implements Iterator, Countable, ArrayAccess
{
    protected $document;
    protected $elements = array();
    protected $select;

    protected $position = 0;

    public function __construct(DOMDocument $document, $elements = null)
    {
        $this->document = $document;
        if ($elements instanceof Traversible)
        {
            foreach ($elements as $element)
            {
                $this->elements[] = new Element($element);
            }
        }

        $this->select = new Selector($this->document, $this);
    }

    public function __invoke($selector)
    {
        return $this->select->execute($selector);
    }

    public function add()
    {
        $elements = func_get_args();

        /*if (count($elements) == 1 && ($elements[0] instanceof DOMNodeList || $elements[0] instanceof self))
        {
            call_user_func_array(array($this, __FUNCTION__), self::toArray($elements[0]));
        }
        else
        {*/

            foreach ($elements as $element)
            {

                if ($element instanceof DOMNodeList || $element instanceof self)
                {
                    call_user_func_array(array($this, __FUNCTION__), self::toArray($element));
                    continue;
                }

                if ($element instanceof DOMElement) $element = new Element($element);
                $this->elements[] = $element;
            }
        //}

        return $this;
    }

    public static function toArray($input)
    {
        $output = array();

        foreach ($input as $i)
        {
            $output[] = $i;
        }

        return $output;
    }

    public function __get($attribute)
    {
        return $this->elements[0]->{$attribute};
    }

    public function __set($attribute, $value)
    {
        foreach ($this->elements as $element)
        {
            $element->{$attribute} = $value;
        }

        return $this;
    }

    public function __call($attribute, $value)
    {
        foreach ($this->elements as $element)
        {
            $element->{$attribute}($value);
        }

        return $this;
    }

    /**
     * Iterator: rewind to first element
     *
     * @return Element
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Iterator: is current position valid?
     *
     * @return bool
     */
    public function valid()
    {
        if (isset($this->elements[$this->position]))
        {
            return true;
        }

        return false;
    }

    /**
     * Iterator: return current element
     *
     * @return Element
     */
    public function current()
    {
        return $this->elements[$this->position];
    }

    /**
     * Iterator: return key of current element
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Iterator: move to next element
     *
     * @return Element
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Countable: get count
     *
     * @return int
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * ArrayAccess: offset exists
     *
     * @param int $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->elements[$key]);
    }

    /**
     * ArrayAccess: get offset
     *
     * @param int $key
     * @return Element
     */
    public function offsetGet($key)
    {
        return $this->elements[$key];
    }

    /**
     * ArrayAccess: set offset
     *
     * @param  mixed $key
     * @param  mixed $value
     * @throws BadMethodCallException when attemptingn to write to a read-only item
     */
    public function offsetSet($key, $value)
    {
        throw new BadMethodCallException('Attempting to write to a read-only list');
    }

    /**
     * ArrayAccess: unset offset
     *
     * @param  mixed $key
     * @throws BadMethodCallException when attemptingn to unset a read-only item
     */
    public function offsetUnset($key)
    {
        throw new BadMethodCallException('Attempting to unset on a read-only list');
    }
}