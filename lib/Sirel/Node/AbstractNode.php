<?php
/**
 * Base Class for all Nodes
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Sirel
 * @package    Sirel
 * @subpackage Node
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) Christoph Hochstrasser
 * @license    MIT License
 */

namespace Sirel\Node;

use Sirel\Visitor\Visitor;

abstract class AbstractNode implements Node
{
    /**
     * Accepts the given Visitor
     * 
     * @param  Visitor $visitor
     * @return mixed Return value of the Visitor's visit() method
     */
    function accept(Visitor $visitor)
    {
        return $visitor->visit($this);
    }
}
