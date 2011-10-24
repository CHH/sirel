<?php
/**
 * Manages INSERT Queries
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

use Sirel\Node\InsertStatement,
    Sirel\Node\UnqualifiedColumn;

class InsertManager extends AbstractManager
{
    function __construct()
    {
        $this->nodes = new InsertStatement;
    }

    /**
     * Insert into this relation
     *
     * @param  Table $relation
     * @return InsertManager
     */
    function into(Table $relation)
    {
        $this->nodes->relation = $relation;
        return $this;
    }

    /**
     * Column-Value-Pairs
     *
     * @param array $values
     * @return InsertManager
     */
    function values(array $values)
    {
        $cols = array();
        $vals = array();

        foreach ($values as $col => $val) {
            if (null !== $val) {
                $cols[] = $col;
                $vals[] = $val;
            }
        }

        foreach ($cols as &$col) {
            $this->nodes->columns[] = new UnqualifiedColumn($col);
        }

        $this->nodes->values = array_merge($this->nodes->values, $vals);
        return $this;
    }

    /**
     * Sets columns for this insert
     *
     * @param  array $columns
     * @return InsertManager
     */
    function columns(array $columns)
    {
        foreach ($columns as &$col) {
            if (!$col instanceof Attribute) {
                $col = new UnqualifiedColumn($col);
            }
        }
        $this->nodes->columns = $columns;
        return $this;
    }

    /**
     * Get values from this Select Query
     *
     * @param  SelectManager $select
     * @return InsertManager
     */
    function select(SelectManager $select)
    {
        $this->nodes->select = $select;
        return $this;
    }
}
