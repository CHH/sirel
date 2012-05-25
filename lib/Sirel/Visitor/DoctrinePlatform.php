<?php

namespace Sirel\Visitor;

use Doctrine\DBAL\Platforms\AbstractPlatform,
    Sirel\Node;

class DoctrinePlatform extends ToSql
{
    protected $platform;

    function __construct(AbstractPlatform $platform)
    {
        $this->platform = $platform;
    }

    /**
     * Creates an UPDATE Statement
     *
     * @param  Node\UpdateStatement
     * @return string
     */
    protected function visitSirelNodeUpdateStatement(Node\UpdateStatement $update)
    {
        $query = join(' ', array_filter(array(
            "UPDATE",
            $this->visit($update->relation),

            // SET
            "SET " . join(', ', $this->visitEach($update->values)),

            // WHERE
            ($update->restrictions 
                ? "WHERE " . join(" AND ", $this->visitEach($update->restrictions))
                : null
            ),

            // ORDER BY
            ($update->orders
                ? "ORDER BY " . join(", ", $this->visitEach($update->orders))
                : null)
        )));

        $query = $this->platform->modifyLimitQuery($query, $update->limit->getExpression(), $update->order->getExpression());
        $query .= ';';

        return $query;
    }

    /**
     * Creates a DELETE Statement
     *
     * @param  Node\DeleteStatement
     * @return string
     */
    protected function visitSirelNodeDeleteStatement(Node\DeleteStatement $delete)
    {
        $query = join(' ', array_filter(array(
            "DELETE FROM " . $this->visit($delete->relation),
            
            // WHERE
            ($delete->restrictions
                ? "WHERE " . join(" AND ", $this->visitEach($delete->restrictions))
                : null
            ),

            // ORDER BY
            ($delete->orders
                ? "ORDER BY " . join(", ", $this->visitEach($delete->orders))
                : null)
        )));

        $query = $this->platform->modifyLimitQuery($query, $delete->limit->getExpression(), $delete->offset->getExpression());
        $query .= ';';

        return $query;
    }
}
