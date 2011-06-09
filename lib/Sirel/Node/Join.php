<?php

namespace Sirel\Node;

abstract class Join extends Binary
{
    function on($expr)
    {
        $this->right = $expr;
        return $this;
    }
}
