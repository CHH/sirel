<?php

namespace Sirel\Node;

/**
 * Base class for criterions which compare the attribute to a given value
 */
abstract class Binary extends AbstractNode
{
    /**
     * @var string
     */
    protected $left;

    /**
     * @var mixed
     */
    protected $right;

    function __construct($left, $right)
    {
        $this->left  = $left;
        $this->right = $right;
    }

    function __clone()
    {
        $this->left = clone $this->left;
        $this->right = clone $this->right;
    }

    function getLeft()
    {
        return $this->left;
    }

    function getRight()
    {
        return $this->right;
    }

    function _and(\Sirel\Node\Node $otherNode)
    {
        return new AndX(array($this, $otherNode));
    }

    function _or(\Sirel\Node\Node $otherNode)
    {
        return new Grouping(new OrX(array($this, $otherNode)));
    }
}
