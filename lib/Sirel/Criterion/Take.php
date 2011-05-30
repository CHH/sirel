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
    protected $numRows;

    /**
     * Constructor
     *
     * @param int $numRows
     */
    function __construct($numRows)
    {
        $this->numRows = $numRows;
    }

    function getNumber()
    {
        return $this->numRows;
    }
}
