<?php

namespace Sirel\Visitor;

use Sirel\Node;

/**
 * Transforms the Query into SQL valid for Amazon Simple Db Select Queries
 */
class AmazonSimpleDb extends AbstractVisitor
{
    protected $currentStatement;

    function visitSirelNodeSelectStatement(Node\SelectStatement $select)
    {
        $this->currentStatement = $select;

        return join(' ', array_filter(array(
            "select"
        )));
    }
}
