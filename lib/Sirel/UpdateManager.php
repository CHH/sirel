<?php
/**
 * Manages UPDATE Queries
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

class UpdateManager extends AbstractManager
{
    use Selections;

    function __construct()
    {
        $this->nodes = new Node\UpdateStatement;
    }

    /**
     * Which table should be updated
     *
     * @param  mixed $relation
     * @return UpdateManager
     */
    function table($relation)
    {
        $this->nodes->relation = $relation;
        return $this;
    }

    /**
     * Values for the Update
     *
     * @param  array $values Column-Value-Pairs
     * @return UpdateManager
     */
    function set(array $values)
    {
        array_walk($values, function(&$val, $key) {
            $key = new Node\UnqualifiedColumn($key);
            $val = new Node\Assignment($key, $val);
        });

        $this->nodes->values = $values;
        return $this;
    }
}
