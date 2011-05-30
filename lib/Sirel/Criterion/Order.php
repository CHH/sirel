<?php

namespace Sirel\Criterion;

class Order implements \Sirel\Criterion
{
    const ASC  =  1;
    const DESC = -1;

    protected $attribute;
    protected $direction;

    function __construct($attribute, $direction = self::ASC)
    {
        if (!in_array($direction, array(self::ASC, self::DESC))) {
            throw new \InvalidArgumentException("Sort Direction $direction is\
                not supported, use Order::ASC or ORDER::DESC instead.");
        }

        $this->attribute = $attribute;
        $this->direction = $direction;
    }

    function getAttribute()
    {
        return $this->attribute;
    }

    function getDirection()
    {
        return $this->direction;
    }
}
