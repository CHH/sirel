<?php

namespace Sirel;

/**
 * A compound data source, which consists of multiple concrete Data Stores.
 *
 * When queried with a SelectQuery it should loop through the data stores
 * until a data store provides a result.
 *
 * If queried with a InsertQuery, UpdateQuery or DeleteQuery it shall
 * loop through all Data Stores and should ensure that all data stores
 * accepted and successfully executed the query.
 *
 * Of course different strategies may be implemented.
 *
 * For example: 
 * We could add two Backends to the MultiDataStore, a Cache 
 * and a Database Backend. When the MultiDataStore is queried, it then queries each
 * Backend. If the Cache answers the Query with a Result, the result of the cache
 * is returned. Otherwise the Query is passed to the Database.
 */
interface MultiDataStore extends DataStore
{
    /**
     * Add a backend to the MultiDataStore
     *
     * @param DataStore $store
     */
    function add(DataStore $store);
}
