<?php

namespace Sirel\Node;

/**
 * Represents the Syntax Tree of a Delete Statement
 */
class DeleteStatement extends AbstractNode
{
    /**
     * @var Table
     */
    public $relation;

    /**
     * @var array
     */
    public $restrictions = array();

    /**
     * @var array
     */
    public $orders = array();

    /**
     * @var Offset
     */
    public $offset;

    /**
     * @var Limit
     */
    public $limit;

    function __clone()
    {
        if (is_object($this->relation)) {
            $this->relation = clone $this->relation;
        }

        if (null !== $this->offset) {
            $this->offset = clone $this->offset;
        }

        if (null !== $this->limit) {
            $this->limit = clone $this->limit;
        }
    }
}
