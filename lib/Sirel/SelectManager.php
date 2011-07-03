<?php
/**
 * Manages SELECT Queries
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

    function join($relation, $expr = null, $mode = Join::INNER)
    {
        return $this->createJoin($relation, $expr, $mode);
    }

    function innerJoin($relation, $expr = null)
    {
        return $this->join($relation, $expr);
    }

    function leftJoin($relation, $expr = null)
    {
        return $this->join($relation, $expr, Join::LEFT);
    }

    /**
     * Sets the Last added Join to natural
     *
     * @param bool $enabled
     * @return SelectManager
     */
    function natural($enabled = true)
    {
        $this->getLastJoinSource()->natural = (bool) $enabled;
        return $this;
    }

    /**
     * Adds Join Constraints to the last added Join Source
     *
     * @param  Node $expr,...
     * @return SelectManager
     */
    function on($expr)
    {
        $lastJoin = $this->getLastJoinSource();

        // Join all given expressions with AND
        if ($lastJoin->right instanceof On) {
            // Merge the new ON Expressions with the 
            // old if there exists an ON Expression
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

    /**
     * Adds the given nodes to the list of projections for this Query
     *
     * @param  array $projections|Node $projection,...
     * @return SelectManager
     */
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
     *
     * @param  Node $expr,...
     * @return SelectManager
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

    /**
     * Adds a Limit Expression
     *
     * @param  int $numRows
     * @return SelectManager
     */
    function take($numRows)
    {
        $this->nodes->limit = $numRows !== null ? new Limit($numRows) : null;
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

    protected function getLastJoinSource()
    {
        $lastJoin = current(array_slice($this->nodes->source->right, -1, 1));

        if (!$lastJoin instanceof Join) {
            throw new UnexpectedValueException(
                "You are not in a Join Operation. Call join() first"
            );
        }

        return $lastJoin;
    }

    /**
     * Creates a Join Expression Instance and adds it to the Join Sources
     *
     * @param mixed      $relation
     * @param Node|array $expr     One Expression, or a list of Expressions
     * @param string     $mode     Type of the Join, defaults to "InnerJoin"
     * @param bool       $natural  Sets the Join to Natural, if true
     *
     * @return SelectManager
     */
    protected function createJoin(
        $relation, $expr = null, $mode = Join::INNER, $natural = false
    )
    {
        if (null !== $expr) {
            $expr = new On((array) $expr);
        }

        $join = new Join($relation, $expr);
        $join->mode = $mode;

        $this->nodes->source->right[] = $join;

        return $this;
    }
}
