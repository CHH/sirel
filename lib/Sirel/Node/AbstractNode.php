<?php

namespace Sirel\Node;

use Sirel\Visitor\Visitor;

abstract class AbstractNode implements Node
{
    function accept(Visitor $visitor)
    {
        $visitor->visit($this);
    }
}
