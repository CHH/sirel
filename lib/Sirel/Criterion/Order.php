<?php

namespace Sirel\Criterion;

use Sirel\AbstractCriterion;

class Order extends AbstractCriterion
{
    const SORT_DESC = "desc";
    const SORT_ASC = "asc";

    public $value = self::SORT_ASC;
}
