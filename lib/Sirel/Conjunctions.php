<?php

namespace Sirel;

use Sirel\Node;

trait Conjunctions
{
    function _and(Node\Node $otherNode)
    {
        return new Node\AndX(array($this, $otherNode));
    }

    function _or(Node\Node $otherNode)
    {
        return new Node\Grouping(new Node\OrX(array($this, $otherNode)));
    }
}
