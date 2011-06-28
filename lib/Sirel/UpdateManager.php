<?php
/**
 * Manages UPDATE Queries
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Sirel
 * @package    Sirel
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) Christoph Hochstrasser
 * @license    MIT License
 */

namespace Sirel;

class UpdateManager extends AbstractManager
{
    function __construct()
    {
        $this->nodes = new Node\UpdateStatement;
    }

    /**
     * Which table should be updated
     *
     * @param  mixed $relation
     * @return UpdateManager
     */
    function table($relation)
    {
        $this->nodes->relation = $relation;
        return $this;
    }

    /**
     * Values for the Update
     *
     * @param  array $values Column-Value-Pairs
     * @return UpdateManager
     */
    function set(array $values)
    {
        array_walk($values, function(&$val, $key) {
            $key = new Node\UnqualifiedColumn($key);
            $val = new Node\Assignment($key, $val);
        });

        $this->nodes->values = $values;
        return $this;
    }

    /**
     * Adds an Expression to the WHERE clause
     *
     * @param  Node $expr
     * @return UpdateManager
     */
    function where($expr)
    {
        foreach (func_get_args() as $expr) {
            if (!$expr instanceof Node\Node) {
                throw new \InvalidArgumentException("Argument is not an Instance of Node");
            }
            $this->nodes->restrictions[] = $expr;
        }
        return $this;
    }

    /**
     * Adds an Order Expression
     *
     * @param  Node|Attribute $expr
     * @param  int            $direction
     * @return UpdateManager
     */
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
