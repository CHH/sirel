<?php

namespace Sirel\Node;

use Sirel\Visitor\ToSql, 
    Sirel\Visitor\Visitor;

class SelectStatement
{
    protected $source;
    protected $criteria;
    protected $orders;
    protected $limit;
    protected $offset;

    function __construct()
    {
        $this->criteria = new WithAnd;
    }

    function accept(Visitor $visitor)
    {
        return $visitor->visit($this);
    }

    function getSource()
    {
        return $this->source;
    }

    function getOrders()
    {
        return $this->orders;
    }

    function getLimit()
    {
        return $this->limit;
    }

    function getOffset()
    {
        return $this->offset;
    }

    function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Which relation should be selected?
     * @param string $relation
     */
    function from($relation)
    {
        $this->source = new JoinSource($relation, null);
        return $this;
    }

    /**
     * Add a node to the criteria
     */
    function where(Node $expr = null)
    {
        if (null === $expr) {
            return $this->criteria;
        }
        $this->criteria->add($expr);
        return $this;
    }

    function orWhere(Node $expr)
    {
        $this->criteria->withOr()->add($expr);
        return $this;
    }

    function order($expr, $direction = null)
    {
        if ($expr instanceof Node\Order) {
            $this->order = $expr;
        } else {
            $this->order = new Order($expr, $direction);
        }
        return $this;
    }

    function take($numRows)
    {
        $this->limit = $numRows !== null ? new Limit($numRows) : null;
        return $this;
    }

    function skip($numRows)
    {
        $this->offset = $numRows !== null ? new Offset($numRows) : null;
        return $this;
    }

    function toSql()
    {
        $visitor = new ToSql;
        return $this->accept($visitor);
    }
}
