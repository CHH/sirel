<?php

namespace Sirel\Criterion;

/**
 * Base class for criterions which compare the attribute to a given value
 */
abstract class AttributeConstraint implements \Sirel\Criterion
{
    /**
     * @var string
     */
    protected $attribute;

    /**
     * @var mixed
     */
    protected $expected;

    function __construct($attribute, $expected)
    {
        $this->attribute = $attribute;
        $this->expected  = $expected;
    }

    function getAttribute()
    {
        return $this->attribute;
    }

    function getExpected()
    {
        return $this->expected;
    }
}
