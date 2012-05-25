<?php

namespace Sirel\Node;

abstract class AbstractNodeList extends AbstractNode
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
