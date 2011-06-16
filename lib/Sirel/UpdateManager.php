<?php

namespace Sirel;

class UpdateManager extends AbstractManager
{
    function __construct()
    {
        $this->nodes = new Node\UpdateStatement;
    }

    function table($relation)
    {
        $this->nodes->relation = $relation;
        return $this;
    }

    function set(array $values)
    {
        array_walk($values, function(&$val, $key) {
            $key = new Node\UnqualifiedColumn($key);
            $val = new Node\Assignment($key, $val);
        });

        $this->nodes->values = $values;
        return $this;
    }

    function where($expr)
    {
        foreach (func_get_args() as $expr) {
            $this->nodes->restrictions[] = $expr;
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

    function take($numRows)
    {
        $this->nodes->limit = new Node\Limit($numRows);
        return $this;
    }

    function skip($numRows)
    {
        $this->nodes->offset = new Node\Offset($numRows);
        return $this;
    }
}
