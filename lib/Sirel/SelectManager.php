<?php

namespace Sirel;

use Sirel\Node\Node,
    Sirel\Node\SelectStatement,
    Sirel\Node\JoinSource,
    Sirel\Node\Order,
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

    /**
     * Add a node to the criteria
     */
    function where(Node $expr = null)
    {
        $this->nodes->criteria[] = $expr;
        return $this;
    }

    function order($expr, $direction = null)
    {
        if (null === $expr) {
            $this->nodes->order = array();

        } else if ($expr instanceof Node\Order) {
            $this->nodes->order[] = $expr;

        } else {
            $this->nodes->order[] = new Order($expr, $direction);
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
