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

    function accept(Visitor $visitor)
    {
        return $this->nodes->accept($visitor);
    }

    /**
     * Return the SQL if the manager is converted to a string
     *
     * @alias  toSql()
     * @return string
     */
    function __toString()
    {
        try {
            return $this->toSql();
        } catch (\Exception $e) {
            return "";
        }
    }

    /**
     * Triggers the generation of SQL
     *
     * @return string
     */
    function toSql()
    {
        $visitor = new ToSql;
        return $this->accept($visitor);
    }
}
