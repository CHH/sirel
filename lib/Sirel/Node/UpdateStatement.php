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
}
