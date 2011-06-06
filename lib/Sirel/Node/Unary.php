<?php

namespace Sirel\Node;

abstract class Unary extends AbstractNode
{
    protected $expression;

    function __construct($expression)
    {
        $this->expression = $expression;
    }

    function getExpression()
    {
        return $this->expression;
    }
}
