<?php

namespace Sirel;

use Sirel\Node\InsertStatement,
    Sirel\Node\UnqualifiedColumn;

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
        $cols = array_keys($values);
        $vals = array_values($values);

        foreach ($cols as &$col) {
            $this->nodes->columns[] = new UnqualifiedColumn($col);
        }

        $this->nodes->values = array_merge($this->nodes->values, $vals);
        return $this;
    }
}
