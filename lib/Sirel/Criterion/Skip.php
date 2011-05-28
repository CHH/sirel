<?php

namespace Sirel\Criterion;

use InvalidArgumentException,
    Sirel\Criterion;

class Skip implements Criterion
{
    /**
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
