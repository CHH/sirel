<?php

namespace Sirel;

/**
 * Container for criteria
 */
interface Criteria extends Criterion, \IteratorAggregate, \ArrayAccess
{
    function add(Criterion $criterion);
}
