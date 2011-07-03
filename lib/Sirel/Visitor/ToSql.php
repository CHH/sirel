<?php
/**
 * Aims to generate SQL92 Standard compliant SQL
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Sirel
 * @package    Sirel
 * @subpackage Visitor
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) Christoph Hochstrasser
 * @license    MIT License
 */

namespace Sirel\Visitor;

use Sirel\Node,
    Sirel\Attribute\Attribute,
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
            "SELECT",

            ($select->projections 
                ? join(", ", $this->visitEach($select->projections))
                : '*'),

            // FROM
            $this->visit($select->source),

            // WHERE
            ($select->restrictions 
                ? "WHERE " . join(" AND ", $this->visitEach($select->restrictions))
                : null
            ),

            // ORDER BY
            ($select->orders
                ? "ORDER BY " . join(", ", $this->visitEach($select->orders))
                : null),

            // GROUP BY
            ($select->groups
                ? "GROUP BY " . join(', ', $this->visitEach($select->groups))
                : null),

            // LIMIT
            ($select->limit ? $this->visit($select->limit) : null),

            // OFFSET 
            ($select->offset ? $this->visit($select->offset) : null)
        )));
    }

    /**
     * Creates an INSERT Statement
     *
     * @param  Node\InsertStatement $insert
     * @return string
     */
    protected function visitSirelNodeInsertStatement(Node\InsertStatement $insert)
    {
        return
            "INSERT INTO " . $this->visit($insert->relation)
            . ' (' . join(', ', $this->visitEach($insert->columns)) . ')'
            . ' VALUES (' . join(', ', $this->visitEach($insert->values)) . ')';
    }

    /**
     * Creates a DELETE Statement
     *
     * @param  Node\DeleteStatement
     * @return string
     */
    protected function visitSirelNodeDeleteStatement(Node\DeleteStatement $delete)
    {
        return join(' ', array_filter(array(
            "DELETE FROM " . $this->visit($delete->relation),
            
            // WHERE
            ($delete->restrictions
                ? "WHERE " . join(" AND ", $this->visitEach($delete->restrictions))
                : null
            ),

            // ORDER BY
            ($delete->orders
                ? "ORDER BY " . join(", ", $this->visitEach($delete->orders))
                : null),
            
            // LIMIT
            ($delete->limit ? $this->visit($delete->limit) : null),

            // OFFSET 
            ($delete->offset ? $this->visit($delete->offset) : null)
        )));
    }

    /**
     * Creates an UPDATE Statement
     *
     * @param  Node\UpdateStatement
     * @return string
     */
    protected function visitSirelNodeUpdateStatement(Node\UpdateStatement $update)
    {
        return join(' ', array_filter(array(
            "UPDATE",
            $this->visit($update->relation),

            // SET
            "SET " . join(', ', $this->visitEach($update->values)),
            
            // WHERE
            ($update->restrictions 
                ? "WHERE " . join(" AND ", $this->visitEach($update->restrictions))
                : null
            ),

            // ORDER BY
            ($update->orders
                ? "ORDER BY " . join(", ", $this->visitEach($update->orders))
                : null),

            // LIMIT
            ($update->limit ? $this->visit($update->limit) : null),

            // OFFSET 
            ($update->offset ? $this->visit($update->offset) : null)
        )));
    }

    /**
     * Returns the SQL for an Unqualified Column
     *
     * @param  Node\UnqualifiedColumn $column
     * @return string
     */
    protected function visitSirelNodeUnqualifiedColumn(Node\UnqualifiedColumn $column)
    {
        return $column->getExpression();
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

    /**
     * Creates an OFFSET Clause
     *
     * @param  Node\Offset $offset
     * @return string
     */
    protected function visitSirelNodeOffset(Node\Offset $offset)
    {
        return "OFFSET " . $this->visit($offset->getExpression());
    }

    /**
     * Groups the expression in parenthesis
     *
     * @param  Node\Grouping $grouping
     * @return string
     */
    protected function visitSirelNodeGrouping(Node\Grouping $grouping)
    {
        return '(' . $this->visit($grouping->getExpression()) . ')';
    }

    /**
     * Joins the children of the node with an AND operator
     *
     * @param  Node\AndX $and
     * @return string
     */ 
    protected function visitSirelNodeAndX(Node\AndX $and)
    {
        return join(" AND ", $this->visitEach($and->getChildren()));
    }

    /**
     * Joins the Node's children with an OR operator
     *
     * @param  Node\OrX $or
     * @return string
     */
    protected function visitSirelNodeOrX(Node\OrX $or) 
    {
        return join(" OR ", $this->visitEach($or->getChildren()));
    }

    /**
     * @fixme Not sure if this is used
     */
    protected function visitSirelNodeGroup(Node\Group $group)
    {
        return $this->visit($group->getExpression());
    }

    /**
     * Create an ORDER Expression
     *
     * @param  Node\Order $order
     * @return string
     */
    protected function visitSirelNodeOrder(Node\Order $order)
    {
        return $this->visit($order->getExpression()) . " "
            . ($order->isAscending() ? "ASC" : "DESC");
    }

    /**
     * Handle an Assignment, join with "="
     *
     * @param  Node\Assignment $assign
     * @return string
     */
    protected function visitSirelNodeAssignment(Node\Assignment $assign)
    {
        return $this->visit($assign->getLeft()) 
            . " = " . $this->visit($assign->getRight());
    }

    /**
     * Transform to SQL Equality, if the right operand is NULL,
     * then an "IS NULL" comparison is generated.
     *
     * @param  Node\Equal $equal
     * @return string
     */
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

    /**
     * Returns the Table's name
     *
     * @param  Table $table
     * @return string
     */
    protected function visitSirelTable(Table $table)
    {
        return $table->getName();
    }

    protected function visitSirelAttributeBooleanAttribute(Attribute $attribute)
    {
        return $this->visitSirelAttributeAttribute($attribute);
    }

    protected function visitSirelAttributeDecimalAttribute(Attribute $attribute)
    {
        return $this->visitSirelAttributeAttribute($attribute);
    }

    protected function visitSirelAttributeFloatAttribute(Attribute $attribute)
    {
        return $this->visitSirelAttributeAttribute($attribute);
    }

    protected function visitSirelAttributeIntegerAttribute(Attribute $attribute)
    {
        return $this->visitSirelAttributeAttribute($attribute);
    }

    protected function visitSirelAttributeStringAttribute(Attribute $attribute)
    {
        return $this->visitSirelAttributeAttribute($attribute);
    }

    protected function visitSirelAttributeTimeAttribute(Attribute $attribute)
    {
        return $this->visitSirelAttributeAttribute($attribute);
    }

    protected function visitSirelAttributeUndefinedAttribute(Attribute $attribute)
    {
        return $this->visitSirelAttributeAttribute($attribute);
    }

    /**
     * Returns the Attribute's fully qualified name
     *
     * @param  Attribute $attribute
     * @return string
     */
    protected function visitSirelAttributeAttribute(Attribute $attribute)
    {
        return $this->visit($attribute->getRelation()) 
            . "." . $attribute->getName();
    }

    /**
     * Returns the FROM part and visits the JOINs
     *
     * @param  Node\JoinSource $joinSource
     * @return string
     */
    protected function visitSirelNodeJoinSource(Node\JoinSource $joinSource)
    {
        $right = $joinSource->getRight();

        return 
            "FROM " 
            . $this->visit($joinSource->getLeft())
            . ($right ? ' ' . join(' ', $this->visitEach($right)) : null);
    }

    /**
     * Creates a JOIN
     *
     * @param  Node\Join $join
     * @return string
     */
    protected function visitSirelNodeJoin(Node\Join $join)
    {
        switch ($join->mode) {
            case Node\Join::INNER:
                $mode = "INNER";
                break;
            case Node\Join::LEFT:
                $mode = "LEFT";
                break;
            case Node\Join::LEFT_OUTER:
                $mode = "LEFT OUTER";
                break;
            case Node\Join::RIGHT:
                $mode = "RIGHT";
                break;
            case Node\Join::OUTER;
                $mode = "OUTER";
                break;
            default:
                $mode = '';
                break;
        }

        return ($join->natural ? 'NATURAL ' : '')
            . ($mode ? $mode . ' ' : '')
            . "JOIN "
            . $this->visit($join->left)
            . ' '
            . $this->visit($join->right);
    }

    /**
     * Visits the ON Expression
     *
     * @param  Node\On $on
     * @return string
     */
    protected function visitSirelNodeOn(Node\On $on)
    {
        return "ON " . $this->visit($on->expression);
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
