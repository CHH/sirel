<?php
/**
 * Base Class for all Visitors
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

use UnexpectedValueException;

/**
 * Base class for visitors
 *
 * @author  Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @license MIT
 * @copyright Copyright (c) 2011 Christoph Hochstrasser
 */
abstract class AbstractVisitor implements Visitor
{
    /**
     * Inspects the Type of the Node and calls the appropiate
     * type-specific Visitor if possible.
     *
     * For example: 
     *   - If the Node is of Class "Sirel\\Node\\Equal", then the
     *     concrete visitor method "visitSirelNodeEqual" gets called
     *
     *   - If the Node is of Type Integer, then the visitor method
     *     "visitInt" gets called (the return value of `gettype()`, with the
     *     first char uppercased)
     *
     * The visitor method always receives the node, unchanged, as first argument.
     * You may enforce more strict typing in the more specific visitor methods.
     *
     * @throws UnexpectedValueException If no visitor for the Class/Type is found
     * @param  mixed $node The Node, can be everything
     * @return mixed The return value of the concrete visitor
     */
    function visit($node)
    {
        $method = "visit";

        if (is_object($node)) {
            $method .= join('', explode("\\", get_class($node)));
        } else {
            $method .= ucfirst(gettype($node));
        }

        if (!is_callable(array($this, $method))) {
            throw new UnexpectedValueException(sprintf(
                "No Visitor Method for Node of Type %s available", 
                is_object($node) ? get_class($node) : gettype($node)
            ));
        }

        return $this->$method($node);
    }

    /**
     * Utility Method for visiting each item in an list
     *
     * @param  array $list
     * @return array Array with the result of each visit
     */
    protected function visitEach($list)
    {
        return array_map(array($this, "visit"), (array) $list);
    }
}
