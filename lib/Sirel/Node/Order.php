<?php

namespace Sirel\Node;

class Order extends Binary
{
    const ASC  =  1;
    const DESC = -1;

    function __construct($left, $right = self::ASC)
    {
        if (!in_array($right, array(self::ASC, self::DESC))) {
            throw new \InvalidArgumentException("Sort Direction $right is\
                not supported, use Order::ASC or ORDER::DESC instead.");
        }
        parent::__construct($left, $right);
    }

    function getExpression()
    {
        return $this->left;
    }

    function getDirection()
    {
        return $this->right;
    }

    function isAscending()
    {
        return self::ASC === $this->getDirection();
    }

    function isDescending()
    {
        return self::DESC === $this->getDirection();
    }

    function reverse()
    {
        if ($this->isAscending()) {
            $this->right = static::DESC;
        } else if ($this->isDescending()) {
            $this->right = static::ASC;
        }
    }
}
