<?php

namespace Sirel;

use Sirel\Node;

trait Comparisons
{
    function eq($right)
    {
        return new Node\Equal($this, $right);
    }

    function notEq($right)
    {
        return new Node\NotEqual($this, $right);
    }

    function gt($right)
    {
        return new Node\GreaterThan($this, $right);
    }

    function gte($right)
    {
        return new Node\GreaterThanEqual($this, $right);
    }

    function lt($right)
    {
        return new Node\LessThan($this, $right);
    }

    function lte($right)
    {
        return new Node\LessThanEqual($this, $right);
    }

    function in($right)
    {
        return new Node\InValues($this, $right);
    }

    function notIn($right)
    {
        return new Node\NotInValues($this, $right);
    }

    function like($right)
    {
        return new Node\Like($this, $right);
    }

    function notLike($right)
    {
        return new Node\NotLike($this, $right);
    }

    function not()
    {
        return new Node\Not($this);
    }
}
