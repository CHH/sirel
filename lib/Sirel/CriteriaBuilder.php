<?php

namespace Sirel;

use Closure,
    ArrayObject,
    InvalidArgumentException,
    Sirel\Criterion\Equals,
    Sirel\Criterion\GreaterThan,
    Sirel\Criterion\GreaterThanEquals,
    Sirel\Criterion\LessThan,
    Sirel\Criterion\LessThanEquals,
    Sirel\Criterion\InValues,
    Sirel\Criterion\MaxResults,
    Sirel\Criterion\All,
    Sirel\Criterion\Any;

class CriteriaBuilder extends ArrayObject implements Criteria
{
    /**
     * Constructor
     *
     * @param Closure $closure A setup function for the Criteria Builder
     */
    function __construct(Closure $closure = null)
    {
        if (null !== $closure and is_callable($closure)) {
            $closure($this);
        }
    }

    function add(Criterion $criterion)
    {
        $this[] = $criterion;
        return $this;
    }

    function offsetSet($offset, $value)
    {
        if (!$value instanceof Criterion) {
            throw new InvalidArgumentException("Value does not implement \\Sirel\\Criterion");
        }
        parent::offsetSet($offset, $value);
    }

    /**
     * Returns the Criteria as List
     */
    function getAll()
    {
        return $this->getArrayCopy();
    }

    /**
     * Returns a Criteria Builder, which chains Criteria with AND
     *
     * @param  Closure $closure A Closure which can be used to setup the Criteria Builder Instance
     * @return All
     */
    function all(Closure $closure = null)
    {
        $criterion = new All($closure);
        $this->add($criterion);
        return $criterion;
    }

    /**
     * Returns a Criteria Builder, which chains Criteria with OR
     *
     * @param  Closure $closure A Closure which can be used to setup the Criteria Builder Instance
     * @return Any
     */
    function any(Closure $closure = null)
    {
        $criterion = new Any($closure);
        $this->add($criterion);
        return $criterion;
    }

    function eq($field, $value)
    {
        return $this->add(new Equals($field, $value));
    }

    function gt($field, $value)
    {
        return $this->add(new GreaterThan($field, $value));
    }

    function gte($field, $value)
    {
        return $this->add(new GreaterThanEquals($field, $value));
    }

    function lt($field, $value)
    {
        return $this->add(new LessThan($field, $value));
    }

    function lte($field, $value)
    {
        return $this->add(new LessThanEquals($field, $value));
    }

    function in($field, array $values)
    {
        return $this->add(new InValues($field, $value));
    }

    function maxResults($number)
    {
        return $this->add(new MaxResults($field, $value));
    }
}
