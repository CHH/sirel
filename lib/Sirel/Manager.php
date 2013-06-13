<?php

namespace Sirel;

use Sirel\Visitor\Visitor;

interface Manager
{
    function getNodes();
    function accept(Visitor $visitor);
    function setVisitor(Visitor $visitor);
    function getVisitor();
    function toSql();
}
