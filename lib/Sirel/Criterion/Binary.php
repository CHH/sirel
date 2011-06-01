<?php

namespace Sirel\Criterion;

/**
 * Base class for criterions which compare the attribute to a given value
 */
abstract class Binary implements \Sirel\Criterion
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

    function getLeft()
    {
        return $this->left;
    }

    function getRight()
    {
        return $this->right;
    }
}
