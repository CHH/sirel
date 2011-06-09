<?php

namespace Sirel;

use BadMethodCallException;

/**
 * Represents the Relation
 *
 * TODO: Add real table introspection support (maybe Doctrine\SchemaManager?)
 *
 * The table acts mainly as a convenience tool for creating 
 * Attribute Instances. Attributes can be accessed either as properties
 * or as Array Indizes.
 * ```
 * $users = new Table("users");
 * var_dump($users['username'] === $users->username);
 * ```
 */
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

    /**
     * Returns a new Select Manager, and selects from this table
     * @return SelectManager
     */
    function from()
    {
        $select = new SelectManager;
        return $select->from($this);
    }

    /**
     * Returns a new Select Manager and adds the given expressions as projections
     *
     * @param  array $projections|mixed $projection,...
     * @return SelectManager
     */
    function project($projections)
    {
        $select = $this->from();
        if (1 === func_num_args() and is_array($projections)) {
            return $select->project($projections);
        } else {
            return $select->project(func_get_args());
        }
    }

    /**
     * Returns a new Select Manager and add the given expressions 
     * to its restrictions.
     *
     * @param mixed $expr,...
     * @return SelectManager
     */
    function where($expr)
    {
        $select = $this->from();
        foreach (func_get_args() as $expr) {
            $select->where($expr);
        }
        return $select;
    }

    /**
     * Creates a new Query and sets and Order Expression
     */
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
    
    /**
     * Allow to access Table Attributes as properties
     * @return Attribute
     */
    function __get($var)
    {
        return $this->offsetGet($var);
    }

    /**
     * Allow to access Table Attributes as array offsets on the table object
     * @return Attribute
     */
    function offsetGet($offset)
    {
        if (empty($this->attributes[$offset])) {
            $this->attributes[$offset] = new Attribute($offset, $this);
        }
        return $this->attributes[$offset];
    }

    function offsetSet($offset, $value)
    {
        throw new BadMethodCallException("offsetSet is not available");
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
