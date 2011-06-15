<?php

namespace Sirel;

use Sirel\Node\DeleteStatement;

class DeleteManager extends AbstractManager
{
    function __construct()
    {
        $this->nodes = new DeleteStatement;
    }

    function from(Table $relation)
    {
    }

    function where(Node\Node $restriction)
    {
    }

    function order(Node\Node $order, $direction = null)
    {
    }

    function take($amount)
    {
    }

    function skip($amount)
    {
    }
}
