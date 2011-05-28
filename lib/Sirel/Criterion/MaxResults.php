<?php

namespace Sirel\Criterion;

class MaxResults implements Criterion
{
    public $results;

    function __construct($results)
    {
        $this->results = $results;
    }
}
