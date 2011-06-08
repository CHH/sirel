<?php

namespace Sirel;

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
     */
    protected $name;
    protected $relation;

    function __construct($name, $relation)
    {
        $this->name = $name;
        $this->relation = $relation;
    }

    function getName()
    {
        return $this->name;
    }

    function getRelation()
    {
        return $this->relation;
    }

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
