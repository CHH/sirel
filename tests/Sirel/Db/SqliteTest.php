<?php

namespace Sirel\Test\Db;

use PDO,
    DateTime,
    Sirel\Test\AbstractDbTestCase,
    Sirel\Table;

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
     */
    function testThrowsSyntaxErrorOnLimitedDelete()
    {
        $users = new Table("users");

        try {
            $this->pdo->exec(
                $users->delete()->take(2)->toSql()
            );

            $this->fail();

        } catch (\PDOException $e) {
            // Expect a Syntax Error
            $this->assertEquals(1, $e->errorInfo[1]);
        }
    }

    function testThrowsSyntaxErrorOnLimitedUpdate()
    {
        $users = new Table("users");

        try {
            $this->pdo->exec(
                $users->update()
                ->set(array('created_at' => new DateTime))
                ->take(2)
                ->toSql()
            );

            $this->fail();

        } catch (\PDOException $e) {
            // Expect a Syntax Error
            $this->assertEquals(1, $e->errorInfo[1]);
        }
    }
}
