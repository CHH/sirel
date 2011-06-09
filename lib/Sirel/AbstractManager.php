<?php

namespace Sirel;

use Sirel\Visitor\Visitor,
    Sirel\Visitor\ToSql;

abstract class AbstractManager
{
    protected $nodes;

    function accept(Visitor $visitor)
    {
        return $visitor->visit($this->nodes);
    }

    /**
     * Return the SQL if the manager is converted to a string
     *
     * @alias  toSql()
     * @return string
     */
    function __toString()
    {
        try {
            return $this->toSql();
        } catch (\Exception $e) {
            return "";
        }
    }

    /**
     * Triggers the generation of SQL
     *
     * @return string
     */
    function toSql()
    {
        $visitor = new ToSql;
        return $this->accept($visitor);
    }
}
