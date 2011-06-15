<?php

namespace Sirel;

use Sirel\Node\InsertStatement;

class InsertManager extends AbstractManager
{
    function __construct()
    {
        $this->nodes = new InsertStatement;
    }

    function into(Table $relation)
    {
        $this->nodes->relation = $relation;
        return $this;
    }

    /**
     * Column-Value-Pairs
     *
     * @param array $values
     * @return InsertManager
     */
    function values(array $values)
    {
    }
}
