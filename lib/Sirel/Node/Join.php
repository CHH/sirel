<?php

namespace Sirel\Node;

class Join extends Binary
{
    /*
     * Join Modes
     */
    const INNER = 0;
    const OUTER = 1;
    const LEFT  = 2;
    const LEFT_OUTER = 3;
    const RIGHT = 4;
    const CROSS = 5;

    /**
     * Is this a natural Join?
     * @var bool
     */
    public $natural = false;
    
    /**
     * Join Mode
     * @var int
     */
    public $mode;
}
