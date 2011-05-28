<?php

namespace Sirel;

/**
 * A Repository for Data
 */
interface DataStore
{
    /**
     * Queries the Data Store. Returns the query's result.
     *
     * The Data Store may also reject Queries of a certain type. 
     * For example a Cache may reject all Select Queries which query 
     * more than an ID.
     *
     * @throws RuntimeException If something goes wrong
     * @param  Query $query
     * @return bool|Result Returns FALSE if the query was rejected
     *                     Returns a Result Object otherwise
     */
    function execute(Query $query);
}
