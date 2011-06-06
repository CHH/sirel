<?php

namespace Sirel\Node;

use Closure,
    ArrayObject,
    InvalidArgumentException,
    Sirel\Visitor\Visitor;

class Criteria extends ArrayObject implements Node
{
    function getChildren()
    {
        return $this->getArrayCopy();
    }

    function accept(Visitor $visitor)
    {
        $visitor->visit($this);
        foreach ($this as $node) {
            $node->accept($visitor);
        }
    }

    function add(Node $node)
    {
	parent::offsetSet(null, $node);
        return $this;
    }

    function offsetSet($offset, $value)
    {
        if (!$value instanceof Node) {
            throw new InvalidArgumentException("Value does not implement \\Sirel\\Criterion");
        }
        parent::offsetSet($offset, $value);
    }

    /**
     * Returns a Criteria Builder, which chains Criteria with AND
     *
     * @param  Closure $closure A Closure which can be used to setup 
     *                          the Criteria Builder Instance
     * @return Criteria Returns a new criteria builder, where every node gets
     *  joined with AND
     */
    function withAnd(Closure $closure = null)
    {
        $criteria = new WithAnd;
        $this->add($criteria);

        if (null !== $closure) {
            $closure($criteria);
            return $this;
        }
        return $criteria;
    }

    /**
     * Returns a Criteria Builder, which chains Criteria with OR
     *
     * @param  Closure $closure A Closure which can be used to setup 
     *                          the Criteria Builder Instance
     * @return Criteria Returns a new criteria builder, where every child
     *  node gets joined with OR
     */
    function withOr(Closure $closure = null)
    {
        $criteria = new WithOr;
        $this->add($criteria);

        if (null !== $closure) {
            $closure($criteria);
            return $this;
        }
        return $criteria;
    }

    /**
     * Field should be equal to the value
     *
     * @param  string $field
     * @param  mixed  $value
     * @return CriteriaBuilder
     */
    function eq($field, $value)
    {
        return $this->add(new Equal($field, $value));
    }

    function notEq($left, $right)
    {
        return $this->add(new NotEqual($left, $right));
    }

    /**
     * Field value should be greater than the value
     *
     * @param  string $field
     * @param  mixed  $value
     * @return CriteriaBuilder
     */
    function gt($field, $value)
    {
        return $this->add(new GreaterThan($field, $value));
    }

    /**
     * Field value should be greater than or equal to the value
     *
     * @param  string $field
     * @param  mixed  $value
     * @return CriteriaBuilder
     */
    function gte($field, $value)
    {
        return $this->add(new GreaterThanEqual($field, $value));
    }

    /**
     * Field value should be less than the value
     *
     * @param  string $field
     * @param  mixed  $value
     * @return CriteriaBuilder
     */
    function lt($field, $value)
    {
        return $this->add(new LessThan($field, $value));
    }

    /**
     * Field value should be less than or equal to the value
     *
     * @param  string $field
     * @param  mixed  $value
     * @return CriteriaBuilder
     */
    function lte($field, $value)
    {
        return $this->add(new LessThanEqual($field, $value));
    }

    /**
     * Field should be one of the values
     *
     * @param  string $field
     * @param  array $values 
     * @return CriteriaBuilder
     */
    function in($field, array $values)
    {
        return $this->add(new InValues($field, $values));
    }

    function notIn($left, $right)
    {
        return $this->add(new NotInValues($left, $right));
    }

    function like($field, $pattern)
    {
        return $this->add(new Like($field, $pattern));
    }

    function notLike($left, $right)
    {
        return $this->add(new NotLike($left, $right));
    }
}
