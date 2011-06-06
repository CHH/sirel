<?php

namespace Sirel\Visitor;

use Sirel\Node,
    Sirel\Attribute;

class ToSql implements Visitor
{
    function visit($node)
    {
        if (is_string($node)) {
            return "'$node'";

        } else if (is_object($node)) {
            $method = "visit" . join('', array_slice(explode("\\", get_class($node)), 1));

            if (is_callable(array($this, $method))) {
                return $this->$method($node);
            }
        }
    }

    protected function visitNodeList($list)
    {
        return array_map(array($this, "visit"), (array) $list);
    }

    protected function visitNodeSelectStatement(Node\SelectStatement $select)
    {
        return join(" ", array_filter(array(
            // SELECT
            "SELECT *",

            // FROM
            $this->visit($select->getSource()),

            // WHERE
            join(" AND ", $this->visitNodeList($select->getCriteria())),
            
            // ORDER BY
            (!$select->getOrders()) ?: 
                "ORDER BY " . join(", ", $this->visitNodeList($select->getOrders())),
        )));
    }

    protected function visitNodeEqual(Node\Equal $equal)
    {
        $left  = $this->visit($equal->getLeft());
        $right = $equal->getRight();

        if ($right === null) {
            return $left . ' IS NULL';
        } else {
            return $left . '=' . $this->visit($right);
        }
    }

    protected function visitAttribute(Attribute $attribute)
    {
        return $attribute->getRelation() . "." . $attribute->getName();
    }

    protected function visitNodeJoinSource(Node\JoinSource $joinSource)
    {
        return "FROM " . $joinSource->getLeft();
    }
}
