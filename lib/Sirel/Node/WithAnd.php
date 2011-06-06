<?php

namespace Sirel\Node;

class WithAnd implements Node
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
