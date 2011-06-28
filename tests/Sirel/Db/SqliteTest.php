<?php

namespace Sirel\Test\Db;

use PDO,
    Sirel\Test\AbstractDbTestCase,
    Sirel\Table,
    Sirel\AbstractManager,
    Sirel as s;

class SqliteTest extends AbstractDbTestCase
{
    protected function initPdo()
    {
        return new PDO("sqlite::memory:");
    }

    /**
     * Seems like PHP's SQLite does not support Limited
     * Deletes and Updates. 
     *
     * See http://www.sqlite.org/compile.html#enable_update_delete_limit.
     *
     * @expectedException \PDOException
     */
    function testThrowsExceptionOnDeleteLimit()
    {
        $users = new Table("users");

        $this->pdo->exec(
            $users->delete()->take(2)->toSql()
        );
    }
}
