<?php
/**
 * Base Class for all AST Managers 
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Sirel
 * @package    Sirel
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) Christoph Hochstrasser
 * @license    MIT License
 */

namespace Sirel;

use Sirel\Visitor\Visitor,
    Sirel\Visitor\ToSql;

abstract class AbstractManager
{
    protected $nodes;
    protected $visitor;

    function accept(Visitor $visitor)
    {
        return $this->nodes->accept($visitor);
    }

    /**
     * Sets a visitor to use when converting to String.
     *
     * @param Visitor $visitor
     * @return void
     */
    function setVisitor(Visitor $visitor)
    {
        $this->visitor = $visitor;
    }

    /**
     * Returns the visitor, by default the ToSql visitor.
     *
     * @return Visitor
     */
    function getVisitor()
    {
        if (null === $this->visitor) {
            $this->visitor = new ToSql;
        }

        return $this->visitor;
    }

    /**
     * Return the SQL if the manager is converted to a string.
     *
     * @alias  toSql()
     * @return string
     */
    function __toString()
    {
        try {
            return $this->toSql();
        } catch (\Exception $e) {
            trigger_error("Exception while invoking visitor: $e", E_USER_WARNING);
            return "";
        }
    }

    /**
     * Triggers the generation of SQL.
     *
     * @return string
     */
    function toSql()
    {
        return $this->accept($this->getVisitor());
    }
}
