<?php

namespace Sirel;

use Closure,
    ArrayObject,
    InvalidArgumentException,
    Sirel\Criterion\Any,
    Sirel\Criterion\All,
    Sirel\Criterion\Equals,
    Sirel\Criterion\GreaterThan,
    Sirel\Criterion\GreaterThanEquals,
    Sirel\Criterion\InValues,
    Sirel\Criterion\LessThan,
    Sirel\Criterion\LessThanEquals,
    Sirel\Criterion\Like,
    Sirel\Criterion\Order
    Sirel\Criterion\Skip,
    Sirel\Criterion\Take;

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
     * Returns a Criteria Builder, which chains Criteria with AND
     *
     * @param  Closure $closure A Closure which can be used to setup 
     *                          the Criteria Builder Instance
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
     * @param  Closure $closure A Closure which can be used to setup 
     *                          the Criteria Builder Instance
     * @return Any
     */
    function any(Closure $closure = null)
    {
        $criterion = new Any($closure);
        $this->add($criterion);
        return $criterion;
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
        return $this->add(new Equals($field, $value));
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
        return $this->add(new GreaterThanEquals($field, $value));
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
        return $this->add(new LessThanEquals($field, $value));
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

    function like($field, $pattern)
    {
        return $this->add(new Like($field, $pattern));
    }

    /**
     * At most {n} rows should be returned
     *
     * @param  int $number
     * @return CriteriaBuilder
     */
    function take($numRows)
    {
        return $this->add(new Take($numRows));
    }

    function skip($numRows)
    {
        return $this->add(new Skip($numRows));
    }

    function order($field, $direction)
    {
        return $this->add(new Order($field, $direction));
    }
}
