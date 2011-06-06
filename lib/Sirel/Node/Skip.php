<?php

namespace Sirel\Criterion;

use InvalidArgumentException,
    Sirel\Criterion;

class Skip implements Criterion
{
    /**
     * @var int
     */
    protected $offset;

    /**
     * Constructor
     *
     * @param int $number
     */
    function __construct($offset)
    {
        $this->offset = $offset;
    }

    function getNumber()
    {
        return $this->offset;
    }
}
