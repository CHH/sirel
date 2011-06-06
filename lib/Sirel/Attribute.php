<?php

namespace Sirel;

use Sirel\Equal,
    Sirel\NotEqual,
    Sirel\GreaterThan,
    Sirel\GreaterThanEqual,
    Sirel\LessThan,
    Sirel\LessThanEqual,
    Sirel\Like,
    Sirel\NotLike,
    Sirel\InValues,
    Sirel\NotInValues,
    Sirel\Not;

class Attribute
{
    /**
     * Name of the Attribute
     */
    protected $name;
    protected $relation;

    function __constuct($name, $relation)
    {
        $this->name = $name;
        $this->relation = $relation;
    }

    function getName()
    {
        return $this->name;
    }

    function __toString()
    {
        return $this->name;
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
