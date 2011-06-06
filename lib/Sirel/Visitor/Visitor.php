<?php

namespace Sirel\Visitor;

use Sirel\Node\Node;

interface Visitor
{
    function visit($node);
}
