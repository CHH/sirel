<?php

namespace Sirel;

interface Query
{
    /**
     * The branch of data within the data source, on which this Query 
     * should operate on, e.g. a Name of a Table or a Key Space in a Cache
     *
     * Data Stores may ignore this if they do not support such a
     * representation of data (e.g. simple Caches like APC).
     */
    function getRelation();
}
