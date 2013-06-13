<?php

namespace Sirel;

use Sirel\Node\Limit;
use Sirel\Node\Order;
use Sirel\Node\Offset;

trait Selections
{
    abstract function getNodes();

    /**
     * Add a node to the criteria
     *
     * @param  Node $expr,...
     * @return SelectManager
     */
    function where($expr)
    {
        foreach (func_get_args() as $expr) {
            $this->getNodes()->restrictions[] = $expr;
        }

        return $this;
    }

    /**
     * Override all previous order clauses with a new one.
     *
     * @see order()
     * @param mixed $expr
     * @param int $direction
     */
    function reorder($expr, $direction = null)
    {
        $this->order(null);
        $this->order($expr, $direction);

        return $this;
    }

    function order($expr, $direction = null)
    {
        if (null === $expr) {
            $this->getNodes()->orders = array();

        } else if ($expr instanceof Order) {
            $this->getNodes()->orders[] = $expr;

        } else {
            $this->getNodes()->orders[] = new Order($expr, $direction);
        }

        return $this;
    }

    /**
     * Reverses the order of all order clauses
     *
     * @param array $attributes Optional list of attributes which should 
     * be reversed
     * @return SelectManager
     */
    function reverseOrder(array $attributes = null)
    {
        if ($attributes !== null) {
            $attributes = array_map('strval', $attributes);

            $orders = array_filter($this->getNodes()->orders, function($o) use ($attributes) {
                $expr = $o->getExpression();
                $name = (string) $expr;

                return in_array($name, $attributes);
            });
        } else {
            $orders = $this->getNodes()->orders;
        }

        foreach ($orders as $order) {
            $order->reverse();
        }

        return $this;
    }

    /**
     * Adds a Limit Expression
     *
     * @param  int $numRows
     * @return SelectManager
     */
    function take($numRows)
    {
        $this->getNodes()->limit = $numRows !== null ? new Limit($numRows) : null;
        return $this;
    }

    /**
     * Adds an Offset Expression
     *
     * @param  int $numRows
     * @return SelectManager
     */
    function skip($numRows)
    {
        $this->getNodes()->offset = $numRows !== null ? new Offset($numRows) : null;
        return $this;
    }
}
