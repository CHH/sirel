<?php

namespace Sirel;

class AbstractCriterion implements Criterion
{
    public $field;
    public $value;

    function __construct($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }
}
