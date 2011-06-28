<?php
/**
 * Calls the supplied callback if a Node is visited
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Sirel
 * @package    Sirel
 * @subpackage Visitor
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) Christoph Hochstrasser
 * @license    MIT License
 */

namespace Sirel\Visitor;

class DepthFirst implements Visitor
{
    /**
     * @var callback
     */
    protected $callback;

    function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException("Argument is not a valid Callback");
        }
        $this->callback = $callback;
    }

    function visit($node)
    {
        return call_user_func($this->callback, $node);
    }
}
