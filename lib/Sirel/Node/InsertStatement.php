<?php

namespace Sirel\Node;

/**
 * Represents the AST of an INSERT Statement
 * 
 * @link http://www.sqlite.org/syntaxdiagrams.html#insert-statement
 */
class InsertStatement extends AbstractNode
{
    /**
     * Relation where the values should be inserted into
     * @var Sirel\Table
     */
    public $relation;

    /**
     * List of values for each column
     * @var array
     */
    public $values = array();

    /**
     * List of columns, which the values should be mapped to
     * @var array
     */
    public $columns = array();

    /**
     * For INSERT INTO ... SELECT ... Statements
     * @var Sirel\SelectManager
     */
    public $select;

    function __clone()
    {
        if (is_object($this->relation)) {
            $this->relation = clone $this->relation;
        }

        if (null !== $this->select) {
            $this->select = clone $this->select;
        }
    }
}
