<?php

namespace Sirel\Node;

use Sirel\Visitor\ToSql, 
    Sirel\Visitor\Visitor;

class SelectStatement
{
    public $projections = array();

    /**
     * @var JoinSource
     */
    public $source;
    
    /**
     * @var array 
     */
    public $criteria = array();

    /**
     * @var Order
     */
    public $order = array();

    /**
     * @var Limit
     */
    public $limit;

    /**
     * @var Offset
     */
    public $offset;
}
