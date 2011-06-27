<?php

namespace Sirel\Test;

use PDO;

abstract class AbstractDbTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    protected $pdo;

    function __construct()
    {
        $this->pdo = $this->initPdo();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo->query("
            CREATE TABLE users (
                id INTEGER PRIMARY_KEY,
                username TEXT,
                password TEXT,
                created_at TEXT,
                display_name TEXT
            )
        ");
    }

    protected function initPdo()
    {}

    protected function getConnection()
    {
        return $this->createDefaultDBConnection($this->pdo, 'sqlite');
    }

    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/fixtures/users.xml');
    }

    protected function fetchAll($query)
    {
        if ($query instanceof AbstractManager) {
            $query = $query->toSql();
        }
        return $this->pdo->query($query)->fetchAll();
    }

    protected function pluckIds(array $results)
    {
        $ids = array_reduce($results, function($return, $v) {
            $return[] = $v['id'];
            return $return;
        }, array());

        return $ids;
    }
}
