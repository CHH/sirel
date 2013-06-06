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

use Sirel\Node\Functions;
use Sirel\Node\Order;

class Attribute
{
    use \Sirel\Comparisons;

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

    function asc()
    {
        return new Order($this, Order::ASC);
    }

    function desc()
    {
        return new Order($this, Order::DESC);
    }

    function count()
    {
        return new Functions\Count(array($this));
    }

    function sum()
    {
        return new Functions\Sum(array($this));
    }

    function min()
    {
        return new Functions\Min(array($this));
    }

    function max()
    {
        return new Functions\Max(array($this));
    }
}
