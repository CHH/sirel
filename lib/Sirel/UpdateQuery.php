<?php

namespace Sirel;

/**
 * Tell the DataStore to update all entries which match
 * the criteria with the new values
 */
interface UpdateQuery extends Query
{
    /**
     * Should return the field => value pairs to update, e.g.
     * `array("username" => "foo")`
     *
     * @return array
     */
    function getValues();

    /**
     * What rows should be updated with the given values?
     * @return Criteria
     */
    function getCriteria();
}
