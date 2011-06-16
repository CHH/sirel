<?php

namespace Sirel;

use Sirel\Node\DeleteStatement;

class DeleteManager extends AbstractManager
{
    function __construct()
    {
        $this->nodes = new DeleteStatement;
    }

    function from($relation)
    {
        $this->nodes->relation = $relation;
        return $this;
    }

    function where(Node\Node $restriction)
    {
        if (1 === func_num_args()) {
            $this->nodes->restrictions[] = $restriction;
            return $this;
        }

        foreach (func_get_args() as $r) {
            $this->where($r);
        }
        return $this;
    }

    function order($expr, $direction = Node\Order::ASC)
    {
        if (null === $expr) {
            $this->nodes->orders = array();
        } else if ($expr instanceof Node\Order) {
            $this->nodes->orders[] = $expr;
        } else {
            $this->nodes->orders[] = new Node\Order($expr, $direction);
        }
        return $this;
    }

    function take($amount)
    {
        $this->nodes->limit = new Node\Limit($amount);
        return $this;
    }

    function skip($amount)
    {
        $this->nodes->offset = new Node\Offset($amount);
        return $this;
    }
}
