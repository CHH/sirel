<?php

namespace Sirel;

/**
 * Tells the DataStore to create a new entry with the
 * given data
 */
interface InsertQuery extends Query
{
    /**
     * Returns Key-Value-Pairs which get inserted into the Data Store
     *
     * For Example:
     * <code>
     * array(
     *   "username" => "johndoe",
     *   "email" => "john.doe@example.com",
     *   "password" => "mysupersecretpassword",
     * )
     * </code>
     *
     * @return array
     */
    function getValues();
}
