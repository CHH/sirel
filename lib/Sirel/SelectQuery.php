<?php

namespace Sirel;

/**
 * A very minimal Interface for Data Retrieval Queries
 *
 * Concrete Data Stores are welcome to provide more advanced interfaces 
 * on top of this, e.g. to support Joins, Grouping, Sorting or 
 * Select Expressions
 */
interface SelectQuery extends Query
{
    /**
     * Returns the Conditions which each result row has to fulfill.
     * @return Criteria
     */
    function getCriteria();
}
