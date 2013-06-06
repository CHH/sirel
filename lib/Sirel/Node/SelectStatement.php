<?php

namespace Sirel\Node;

class SelectStatement extends AbstractNode
{
    public $projections = array();

    /**
     * @var JoinSource
     */
    public $source;
    
    /**
     * @var array 
     */
    public $restrictions = array();

    /**
     * @var array
     */
    public $orders = array();

    /**
     * @var array
     */
    public $groups = array();

    /**
     * @var Limit
     */
    public $limit;

    /**
     * @var Offset
     */
    public $offset;

    function __clone()
    {
        $this->projections = array_map(function($p) {
            if (is_object($p)) {
                return clone $p;
            } else {
                return $p;
            }
        }, $this->projections);

        if (null !== $this->source) {
            $this->source = clone $this->source;
        }

        if (null !== $this->limit) {
            $this->limit = clone $this->limit;
        }

        if (null !== $this->offset) {
            $this->offset = clone $this->offset;
        }
    }
}
