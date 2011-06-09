<?php

namespace Sirel\Node;

/**
 * Represents the Syntax Tree of a Delete Statement
 */
class DeleteStatement implements Node
{
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
     * @var Offset
     */
    public $offset;

    /**
     * @var Limit
     */
    public $limit;
}
