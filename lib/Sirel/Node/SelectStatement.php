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
}
