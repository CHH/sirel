<?php
/**
 * Represents a Relation's Attribute and provides Factory Methods
 * for common Expressions.
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Sirel
 * @package    Sirel
 * @subpackage Visitor
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) Christoph Hochstrasser
 * @license    MIT License
 */

namespace Sirel\Attribute;

use Sirel\Node\Equal,
    Sirel\Node\NotEqual,
    Sirel\Node\GreaterThan,
    Sirel\Node\GreaterThanEqual,
    Sirel\Node\LessThan,
    Sirel\Node\LessThanEqual,
    Sirel\Node\Like,
    Sirel\Node\NotLike,
    Sirel\Node\InValues,
    Sirel\Node\NotInValues,
    Sirel\Node\Not,
    Sirel\Node\Order,
    Sirel\Node\AndX,
    Sirel\Node\OrX,
    Sirel\Node\Grouping;

class Attribute
{
    /**
     * Name of the Attribute
     * @var string
     */
    protected $name;

    /**
     * Relation, which this attribute belong to
     * @var Table|string
     */
    protected $relation;

    /**
     * Constructor
     *
     * @param string $name
     * @param Table|string $relation
     */
    function __construct($name, $relation = null)
    {
        $this->name = $name;
        $this->relation = $relation;
    }

    /**
     * Attribute Name
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Relation this attribute belongs to
     * @return Table|string
     */
/**
 * Calls the supplied callback if a Node is visited
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Sirel
 * @package    Sirel
 * @subpackage Visitor
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) Christoph Hochstrasser
 * @license    MIT License
 */
    function getRelation()
    {
        return $this->relation;
    }

    function setRelation($relation)
    {
        $this->relation = $relation;
        return $this;
    }

    /**
     * Returns the attributes fully qualified name for debugging
     * @return string
     */
    function __toString()
    {
        return $this->relation . '.' . $this->name;
    }

    function eq($right)
    {
        return new Equal($this, $right);
    }

    function notEq($right)
    {
        return new NotEqual($this, $right);
    }

    function gt($right)
    {
        return new GreaterThan($this, $right);
    }

    function gte($right)
    {
        return new GreaterThanEqual($this, $right);
    }

    function lt($right)
    {
        return new LessThan($this, $right);
    }

    function lte($right)
    {
        return new LessThanEqual($this, $right);
    }

    function in($right)
    {
        return new InValues($this, $right);
    }

    function notIn($right)
    {
        return new NotInValues($this, $right);
    }

    function like($right)
    {
        return new Like($this, $right);
    }

    function notLike($right)
    {
        return new NotLike($this, $right);
    }

    function asc()
    {
        return new Order($this, Order::ASC);
    }

    function desc()
    {
        return new Order($this, Order::DESC);
    }
}
