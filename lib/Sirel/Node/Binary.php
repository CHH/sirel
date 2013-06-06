<?php

namespace Sirel\Node;

/**
 * Base class for criterions which compare the attribute to a given value
 */
abstract class Binary extends AbstractNode
{
    use \Sirel\Conjunctions;

    /**
     * @var string
     */
    public $left;

    /**
     * @var mixed
     */
    public $right;

    function __construct($left, $right)
    {
        $this->left  = $left;
        $this->right = $right;
    }

    function getLeft()
    {
        return $this->left;
    }

    function getRight()
    {
        return $this->right;
    }
}
