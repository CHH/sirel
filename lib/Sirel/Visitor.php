<?php

namespace Sirel;

interface Visitor
{
    function accept(Criterion $criterion);
}
