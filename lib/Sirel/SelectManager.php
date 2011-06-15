<?php

namespace Sirel;

use Sirel\Node\Node,
    Sirel\Node\SelectStatement,
    Sirel\Node\JoinSource,
    Sirel\Node\Order,
    Sirel\Node\Group,
    Sirel\Node\Offset,
    Sirel\Node\Limit,
    Sirel\Visitor\Visitor,
    Sirel\Visitor\ToSql;

class SelectManager extends AbstractManager
{
    function __construct()
    {
        $this->nodes = new SelectStatement;
    }

    /**
     * Which relation should be selected?
     * @param string $relation
     */
    function from($relation)
    {
        $this->nodes->source = new JoinSource($relation, null);
        return $this;
    }

    function project($projections)
    {
        if (!is_array($projections)) {
            $projections = func_get_args();
        }

        foreach ($projections as $projection) {
            $this->nodes->projections[] = $projection;
        }
        return $this;
    }

    /**
     * Add a node to the criteria
     */
    function where(Node $expr)
    {
        foreach (func_get_args() as $expr) {
            if (!$expr instanceof Node) {
                throw new \InvalidArgumentException(
                    "Not an instance of Node given as Restriction"
                );
            }
            $this->nodes->restrictions[] = $expr;
        }
        return $this;
    }

    function order($expr, $direction = null)
    {
        if (null === $expr) {
            $this->nodes->orders = array();

        } else if ($expr instanceof Order) {
            $this->nodes->orders[] = $expr;

        } else {
            $this->nodes->orders[] = new Order($expr, $direction);
        }
        return $this;
    }

    /**
     * Adds a Group expression
     * @param  mixed $expr
     * @return SelectManager
     */
    function group($expr)
    {
        if (null === $expr) {
            $this->nodes->groups = array();
        } else {
            $this->nodes->groups[] = new Group($expr);
        }
        return $this;
    }

    function take($numRows)
    {
        $this->nodes->limit = $numRows !== null ? new Limit($numRows) : null;
        return $this;
    }

    function skip($numRows)
    {
        $this->nodes->offset = $numRows !== null ? new Offset($numRows) : null;
        return $this;
    }

    /**
     * Compiles an Update Query from the restrictions, orders, limits,
     * and offsets of this Select Query.
     *
     * @return UpdateManager
     */
    function compileUpdate()
    {
        $updateManager = new UpdateManager;
        $updateManager->table($this->nodes->source->getLeft());

        foreach ($this->nodes->restrictions as $expr) {
            $updateManager->where($expr);
        }

        foreach ($this->nodes->order as $order) {
            $updateManager->order($order);
        }

        $updateManager->take($this->node->limit->getExpression());
        $updateManager->skip($this->node->offset->getExpression());

        return $updateManager;
    }
}
