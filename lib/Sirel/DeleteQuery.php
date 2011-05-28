<?php

namespace Sirel;

/**
 * Tell the DataStore to delete all entries which 
 * match the given criteria
 */
interface DeleteQuery extends Query
{
    /**
     * What rows should be deleted?
     * @return Criteria
     */
    function getCriteria();
}
