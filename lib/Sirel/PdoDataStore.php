<?php

namespace Sirel;

use PDO,
    InvalidArgumentException;

class PdoDataStore implements DataStore
{
    /** @var PDO */
    protected $pdo;

    function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    function execute(Query $query)
    {
        $sql = $this->toSql($query);
    }

    protected function toSql(Query $query)
    {
        switch (true) {
            case ($query instanceof InsertQuery):
                return $this->insertToSql($query);
                
            case ($query instanceof UpdateQuery):
                return $this->updateToSql($query);
                
            case ($query instanceof SelectQuery):
                return $this->selectToSql($query);
                
            case ($query instanceof DeleteQuery):
                return $this->deleteToSql($query);

            default:
                throw new InvalidArgumentException("Invalid Query instance " . getclass($query));
        }
    }

    protected function insertToSql(InsertQuery $query)
    {
        $relation = $query->getRelation();
        $values = $query->getValues();
        
        foreach ($values as $key => &$value) {
            $value = $key . '=' . $value;
        }
        
        $query = "INSERT INTO $relation SET " . join(', ', $values);
        return $query;
    }

    protected function updateToSql(UpdateQuery $query)
    {
        
    }

    protected function selectToSql(SelectQuery $query)
    {
    }

    protected function deleteToSql(DeleteQuery $query)
    {
    }

    protected function criteriaToSql(Criteria $criteria)
    {
        $sql = "";
        $join = "";
        foreach ($criteria as $criterion) {
            if ($criterion instanceof Criterion\Equals) {
                $sql .= $join . $criterion->getField() . '=' . $criterion->getValue();
                $join = "AND";
            }
        }
        return $sql;
    }
}
