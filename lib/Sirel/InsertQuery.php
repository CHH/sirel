<?php

namespace Sirel;

/**
 * Tells the DataStore to create a new entry with the
 * given data
 */
interface InsertQuery extends Query
{
    /**
     * Should return the values to insert
     * @return array
     */
    function getValues();
}
