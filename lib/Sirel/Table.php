<?php

namespace Sirel;

use BadMethodCallException;

class Table implements \ArrayAccess
{
    /**
     * Attribute instance cache
     */
    protected $attributes = array();

    /**
     * Table Name
     * @var string
     */
    protected $name;

    function __construct($name)
    {
        $this->name = $name;
    }

    function getName()
    {
        return $this->name;
    }

    function from()
    {
        $select = new SelectManager;
        return $select->from($this);
    }

    function project($projections)
    {
        $select = $this->from();
        if (1 === func_num_args() and is_array($projections)) {
            return $select->project($projections);
        } else {
            return $select->project(func_get_args());
        }
    }

    function where($expr)
    {
        $select = $this->from();
        foreach (func_get_args() as $expr) {
            $select->where($expr);
        }
        return $select;
    }

    function order($expr, $direction = \Sirel\Node\Order::ASC)
    {
        return $this->from()->order($expr, $direction);
    }

    function take($expr)
    {
        return $this->from()->take($expr);
    }

    function skip($expr)
    {
        return $this->from()->skip($expr);
    }

    function offsetSet($offset, $value)
    {
        throw new BadMethodCallException("offsetSet is not available");
    }

    function offsetGet($offset)
    {
        if (empty($this->attributes[$offset])) {
            $this->attributes[$offset] = new Attribute($offset, $this);
        }
        return $this->attributes[$offset];
    }

    function offsetExists($offset)
    {
        return true;
    }

    function offsetUnset($offset)
    {
        throw new BadMethodCallException("offsetUnset is not available");
    }
}
