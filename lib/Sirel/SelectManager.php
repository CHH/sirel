<?php

namespace Sirel;

use UnexpectedValueException,
    Sirel\Node\Node,
    Sirel\Node\SelectStatement,
    Sirel\Node\JoinSource,
    Sirel\Node\Join,
    Sirel\Node\InnerJoin,
    Sirel\Node\Order,
    Sirel\Node\Group,
    Sirel\Node\AndX,
    Sirel\Node\On,
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
        $this->nodes->source = new JoinSource($relation, array());
        return $this;
    }

    function join($relation, $expr = null, $class = "InnerJoin")
    {
        return $this->createJoin($relation, $expr, $class);
    }

    protected function createJoin($relation, $expr = null, $class = "InnerJoin")
    {
        if (null !== $expr) {
            $expr = new On((array) $expr);
        }

        $fullClass = "\\Sirel\\Node\\$class";
        $join = new $fullClass($relation, $expr);

        $this->nodes->source->right[] = $join;

        return $this;
    }

    function on($expr)
    {
        $lastJoin = end($this->nodes->source->right);
        reset($this->nodes->source->right);

        if (!$lastJoin instanceof Join) {
            throw new UnexpectedValueException(
                "You are not in a Join Operation. Call join() first"
            );
        }

        // Join all given expressions with AND
        if ($lastJoin->right instanceof On) {
            // Merge the new ON Expressions with the old if there exists an ON Expression
            $lastJoin->right->expression->children = array_merge(
                $lastJoin->right->expression->children, 
                func_get_args()
            );
        } else {
            // Otherwise create a new ON Expression
            $exprs = new On(new AndX(func_get_args()));
        }

        // Add the expressions to the on part of the last added Join Expression
        $lastJoin->right = $exprs;
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

        foreach ($this->nodes->orders as $order) {
            $updateManager->order($order);
        }

        empty($this->nodes->limit) ?:
            $updateManager->take($this->nodes->limit->getExpression());

        empty($this->nodes->offset) ?:
            $updateManager->skip($this->nodes->offset->getExpression());

        return $updateManager;
    }
}
