<?php
/**
 * Manages DELETE Queries
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

use Sirel\Node\DeleteStatement;

class DeleteManager extends AbstractManager
{
    use Selections;

    function __construct()
    {
        $this->nodes = new DeleteStatement;
    }

    function from($relation)
    {
        $this->nodes->relation = $relation;
        return $this;
    }
}
