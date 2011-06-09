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

class SelectManager implements \IteratorAggregate, \Countable
{
    protected $nodes;

    function __construct()
    {
        $this->nodes = new SelectStatement;
    }

    /**
     * Allows to directly iterate over the query
     *
     * @return \Iterator
     */
    function getIterator()
    {
    }

    /**
     * How many rows are in the result?
     * @return int
     */
    function count()
    {
    }

    function accept(Visitor $visitor)
    {
        return $visitor->visit($this->nodes);
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
     * Return the SQL if the manager is converted to a string
     *
     * @alias  toSql()
     * @return string
     */
    function __toString()
    {
        try {
            return $this->toSql();
        } catch (\Exception $e) {
            return "";
        }
    }

    /**
     * Triggers the generation of SQL
     *
     * @return string
     */
    function toSql()
    {
        $visitor = new ToSql;
        return $this->accept($visitor);
    }
}
