<?php

namespace Sirel\Visitor;

use Sirel\Node,
    Sirel\Attribute,
    Sirel\Table;

class ToSql extends AbstractVisitor
{
    /**
     * Utility Method for visiting each item in an list
     *
     * @param  array $list
     * @return array Array with the result of each visit
     */
    protected function visitEach($list)
    {
        return array_map(array($this, "visit"), (array) $list);
    }

    /**
     * Utility method for visiting Boolean operators (Equal, GreaterThan,...)
     *
     * @param  Node\Binary $node A binary node
     * @param  string $operator SQL boolean operator
     * @return string
     */
    protected function visitBooleanOperator(Node\Binary $node, $operator)
    {
        return $this->visit($node->getLeft()) 
            . " $operator "
            . $this->visit($node->getRight());
    }

    /**
     * Get a SELECT Query
     *
     * @param  Node\SelectStatement $select
     * @return string
     */
    protected function visitSirelNodeSelectStatement(Node\SelectStatement $select)
    {
        return join(" ", array_filter(array(
            // SELECT
            "SELECT",

            ($select->projections 
                ? join(", ", $this->visitEach($select->projections))
                : '*'),

            // FROM
            $this->visit($select->source),

            // WHERE
            ($select->criteria 
                ? "WHERE " . join(" AND ", $this->visitEach($select->criteria))
                : null
            ),
            
            // ORDER BY
            ($select->order 
                ? "ORDER BY " . join(", ", $this->visitEach($select->order))
                : null),

            // LIMIT
            ($select->limit ? $this->visit($select->limit) : null),

            // OFFSET 
            ($select->offset ? $this->visit($select->offset) : null)
        )));
    }

    /**
     * Get a LIMIT Clause
     *
     * @param  Node\limit $limit
     * @return string 
     */
    protected function visitSirelNodeLimit(Node\Limit $limit)
    {
        return "LIMIT " . $this->visit($limit->getExpression());
    }

    protected function visitSirelNodeOffset(Node\Offset $offset)
    {
        return "OFFSET " . $this->visit($offset->getExpression());
    }

    protected function visitSirelNodeGrouping(Node\Grouping $grouping)
    {
        return '(' . $this->visit($grouping->getExpression()) . ')';
    }

    protected function visitSirelNodeAndX(Node\AndX $and)
    {
        return join(" AND ", $this->visitEach($and->getChildren()));
    }

    protected function visitSirelNodeOrX(Node\OrX $or) 
    {
        return join(" OR ", $this->visitEach($or->getChildren()));
    }

    protected function visitSirelNodeOrder(Node\Order $order)
    {
        return $this->visit($order->getExpression()) . " "
            . ($order->isAscending() ? "ASC" : "DESC");
    }

    protected function visitSirelNodeEqual(Node\Equal $equal)
    {
        $left  = $this->visit($equal->getLeft());
        $right = $equal->getRight();

        if ($right === null) {
            return $left . ' IS NULL';
        } else {
            return $left . ' = ' . $this->visit($right);
        }
    }

    protected function visitSirelNodeNotEqual(Node\NotEqual $notEqual)
    {
        return $this->visitBooleanOperator($notEqual, "<>");
    }

    protected function visitSirelNodeGreaterThan(Node\GreaterThan $gt)
    {
        return $this->visitBooleanOperator($gt, '>');
    }

    protected function visitSirelNodeGreaterThanEqual(Node\GreaterThanEqual $gte)
    {
        return $this->visitBooleanOperator($gte, ">=");
    }

    protected function visitSirelNodeLessThan(Node\LessThan $lt)
    {
        return $this->visitBooleanOperator($lt, '<');
    }

    protected function visitSirelNodeLessThanEqual(Node\LessThanEqual $lte)
    {
        return $this->visitBooleanOperator($lte, "<=");
    }

    protected function visitSirelNodeInValues(Node\InValues $in)
    {
        return $this->visit($in->getLeft()) 
            . " IN (" 
            . $this->visit($in->getRight())
            . ')';
    }

    protected function visitSirelNodeNotInValues(Node\NotInValues $notIn)
    {
        return $this->visit($notIn->getLeft())
            . " NOT IN ("
            . $this->visit($notIn->getRight())
            . ')';
    }

    /**
     * SQL Literal, return the raw expression
     * @return string
     */
    protected function visitSirelNodeSqlLiteral(Node\SqlLiteral $sqlLiteral)
    {
        return $sqlLiteral->getExpression();
    }

    protected function visitSirelNodeLike(Node\Like $like)
    {
        return $this->visitBooleanOperator($like, "LIKE");
    }

    protected function visitSirelNodeNotLike(Node\NotLike $notLike)
    {
        return $this->visitBooleanOperator($notLike, "NOT LIKE");
    }

    protected function visitSirelTable(Table $table)
    {
        return $table->getName();
    }

    protected function visitSirelAttribute(Attribute $attribute)
    {
        return $this->visit($attribute->getRelation()) 
            . "." . $attribute->getName();
    }

    protected function visitSirelNodeJoinSource(Node\JoinSource $joinSource)
    {
        return "FROM " . $this->visit($joinSource->getLeft());
    }

    protected function visitInteger($node)
    {
        return $node;
    }

    protected function visitString($node)
    {
        return "'" . $node . "'";
    }

    protected function visitFloat($node)
    {
        return $node;
    }

    protected function visitArray(array $node)
    {
        return join(', ', $node);
    }

    protected function visitArrayObject(\ArrayObject $node)
    {
        return join(', ', (array) $node);
    }

    protected function visitDateTime(\DateTime $dateTime)
    {
        return $this->visit($dateTime->format("Y-m-d H:i:s"));
    }
}
