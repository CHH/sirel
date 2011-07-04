<?php
/**
 * Helper Functions and Version Information
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

use Sirel\Node\SqlLiteral;

class Sirel
{
    /**
     * Version, for use with version_compare
     * @const string
     */
    const VERSION = "0.1.0dev";

    /**
     * Marks the provided raw SQL as safe, by wrapping it
     * inside an SqlLiteral Instance
     *
     * @param  string $rawSql String of raw SQL, which should be marked as safe
     * @return SqlLiteral
     */
    static function sql($rawSql)
    {
        return new SqlLiteral($rawSql);
    }

    /**
     * Returns the '*' quantifier wrapped as SQL Literal
     * for use in projections
     *
     * @return SqlLiteral
     */
    static function star()
    {
        return static::sql('*');
    }

    /**
     * @return SelectManager
     */
    static function createSelect()
    {
        return new SelectManager;
    }

    /**
     * @return InsertManager
     */
    static function createInsert()
    {
        return new InsertManager;
    }

    /**
     * @return UpdateManager
     */
    static function createUpdate()
    {
        return new UpdateManager;
    }

    /**
     * @return DeleteManager
     */
    static function createDelete()
    {
        return new DeleteManager;
    }
}
