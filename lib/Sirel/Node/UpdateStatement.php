<?php

namespace Sirel\Node;

class UpdateStatement extends AbstractNode
{
    /**
     * @var Table
     */
    public $relation;

    /**
     * @var Set
     */
    public $values;

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

        $this->restrictions = array_map(function($r) {
            return clone $r;
        });

        $this->orders = array_map(function($o) {
            return clone $o;
        });

        if (null !== $this->values) {
            $this->values = clone $this->values;
        }

        if (null !== $this->offset) {
            $this->offset = clone $this->offset;
        }

        if (null !== $this->limit) {
            $this->limit = clone $this->limit;
        }
    }
}
