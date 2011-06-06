<?php

namespace Sirel\Node;

class WithOr extends Criteria
{
    protected $children = array();

    function __construct(array $children)
    {
        $this->children = $children;
    }

    function getChildren()
    {
        return $this->children;
    }
}
