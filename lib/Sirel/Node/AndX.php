<?php

namespace Sirel\Node;

class AndX implements Node
{
    public $children = array();

    function __construct(array $children)
    {
        $this->children = $children;
    }

    function getChildren()
    {
        return $this->children;
    }
}
