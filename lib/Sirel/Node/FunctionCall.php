<?php

namespace Sirel\Node;

class FunctionCall extends AbstractNodeList
{
    public $name;

    function __construct($name, array $arguments = array())
    {
        $this->name = $name;
        $this->children = $arguments;
    }

    function getArguments()
    {
        return $this->children;
    }
}
