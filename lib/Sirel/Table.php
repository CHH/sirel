<?php
/**
 * Represents the Relation and provides factory methods
 * for creating common AST managers.
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Sirel
 * @package    Sirel
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) Christoph Hochstrasser
 * @license    MIT License
 */

namespace Sirel;

use BadMethodCallException,
    UnexpectedValueException,
    InvalidArgumentException,
    Sirel\Node\SqlLiteral,
    Sirel\Attribute\Attribute;

/**
 * Represents the Relation
 *
 * The table acts mainly as a convenience tool for creating 
 * Attribute Instances. Attributes can be accessed either as properties
 * or as Array Indizes.
 *
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

    /**
     * If Enabled throws an Exception when an undefined Attribute is 
     * accessed. Creates generic Attributes on demand if set to FALSE.
     *
     * Off by Default.
     *
     * @var bool
     */
    protected $strictScheme = false;

    /**
     * Constructor
     *
     * @param string $name Table Name 
     */
    function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Enable Strict Scheme Mode for retrieving Attributes
     *
     * @param  bool  $enabled
     * @return Table
     */
    function setStrictScheme($enabled = true)
    {
        $this->strictScheme = $enabled;
        return $this;
    }

    /**
     * Returns the Table Name
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Returns a new Select Manager and selects this table
     * @return SelectManager
     */
    function from()
    {
        $select = new SelectManager;
        return $select->from($this);
    }

    /**
     * Returns a new Update Manager
     * @return UpdateManager
     */
    function update()
    {
        $updateManager = new UpdateManager;
        return $updateManager->table($this);
    }

    /**
     * Returns a new Delete Manager
     * @return DeleteManager
     */
    function delete()
    {
        $deleteManager = new DeleteManager;
        return $deleteManager->from($this);
    }

    /**
     * Returns a new Manager for Insert Queries
     * @return InsertManager
     */
    function insert()
    {
        $insertManager = new InsertManager;
        return $insertManager->into($this);
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
     * Create a new Select Manager and join the given relation
     *
     * @param  mixed $relation
     * @return SelectManager
     */
    function join($relation)
    {
        return $this->from()->join($relation);
    }

    /**
     * Create a new Select Manager and add the given expressions 
     * to its restrictions.
     *
     * @param  mixed $expr,...
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
     * Create a new Select Manager and with an Order Expression
     *
     * @param  mixed  $expr      Order Expression or Sort Column
     * @param  string $direction Optional, Order Direction if an Sort Column
     *                           is given as first argument
     * @return SelectManager
     */
    function order($expr, $direction = \Sirel\Node\Order::ASC)
    {
        return $this->from()->order($expr, $direction);
    }

    /**
     * Create a new Select Manager and group by the given Attribute
     *
     * @param mixed $expr Group Expression
     * @return SelectManager
     */
    function group($expr)
    {
        return $this->from()->group($expr);
    }

    /**
     * Create a new Select Manager and limit the number of
     * rows returned to the given amount.
     *
     * @param  int $amount
     * @return SelectManager
     */
    function take($amount)
    {
        return $this->from()->take($amount);
    }

    /**
     * Create a new Select Manager and skip
     * the given amount of rows
     *
     * @param  int $amount
     * @return SelectManager
     */
    function skip($amount)
    {
        return $this->from()->skip($amount);
    }

    /**
     * Predefine an Attribute with the given Instance
     *
     * @param  Attribute $attribute
     * @return Table
     */
    function addAttribute(Attribute $attribute)
    {
        $attribute->setRelation($this);
        $this->attributes[$attribute->getName()] = $attribute;
        return $this;
    }

    /**
     * Returns all defined Attributes
     * @return array<Attribute>
     */
    function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Allow to access Table Attributes as properties
     *
     * @throws UnexpectedValueException If $strictScheme is ON and the
     *                                  Property is not defined
     * @return Attribute
     */
    function __get($var)
    {
        return $this->offsetGet($var);
    }

    /**
     * Returns the Table Name for debugging purposes
     * @return string
     */
    function __toString()
    {
        return $this->name;
    }

    /**
     * Allow to access Table Attributes as array offsets on the table object
     *
     * @thorws UnexpectedValueException If $strictScheme is ON and the
     *                                  Offset is not defined
     * @return Attribute
     */
    function offsetGet($offset)
    {
        if (empty($this->attributes[$offset])) {
            if ($this->strictScheme) {
                throw new UnexpectedValueException(
                    "Strict Scheme: Attribute $offset is not defined."
                    . " Please define it before accessing it."
                );
            }
            $this->attributes[$offset] = new Attribute($offset, $this);
        }
        return $this->attributes[$offset];
    }

    /**
     * Define the given Attribute on the Table
     * 
     * @param string    $offset 
     * @param Attribute $value
     */
    function offsetSet($offset, $value)
    {
        if (!$value instanceof Attribute) {
            throw new InvalidArgumentException(
                "Value is not an instance of \\Sirel\\Attribute"
            );
        }
        $this->attributes[$offset] = $value;
    }

    function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    function offsetUnset($offset)
    {
        throw new BadMethodCallException("offsetUnset is not available");
    }
}
