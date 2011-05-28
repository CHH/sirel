<?php

namespace Sirel\Criterion;

use InvalidArgumentException,
    Sirel\Criterion;

class Take implements Criterion
{
    /**
     * How many results should be returned?
     * @var int
     */
    public $value;

    /**
     * Constructor
     *
     * @param int $number
     */
    function __construct($numRows)
    {
        $this->value = $numRows;
    }
}
