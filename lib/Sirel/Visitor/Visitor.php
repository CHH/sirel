<?php
/**
 * Interface for Visitors
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

use Sirel\Node\Node;

interface Visitor
{
    function visit($node);
}
