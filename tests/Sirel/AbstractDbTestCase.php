<?php

namespace Sirel\Test;

use PDO,
    Sirel as s,
    Sirel\AbstractManager,
    Sirel\Table;

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

        $this->pdo->query("
            CREATE TABLE profiles (
                id INTEGER PRIMARY_KEY,
                user_id INTEGER,
                birth_date TEXT
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

    function testSelect()
    {
        $users = new Table("users");

        $result = $this->fetchAll($users->project(s\star()));
        $this->assertEquals(3, count($result));
    }

    function testSelectLimitOffset()
    {
        $users = new Table("users");

        $result = $this->fetchAll(
            $users->project(s\star())->take(2)->skip(1)
        );

        $this->assertEquals(2, count($result));
        $this->assertEquals(array(2, 3), $this->pluckIds($result));
    }

    function testInnerJoin()
    {
        $users = new Table("users");
        $profiles = new Table("profiles");

        $result = $this->fetchAll(
            $users->join($profiles)->on($users['id']->eq($profiles['user_id']))
        );

        $this->assertEquals(2, count($result));
        $this->assertEquals(array(1, 2), $this->pluckIds($result));

        $this->assertEquals("John Doe", $result[0]['display_name']);
        $this->assertEquals("1970-01-01 00:00:01", $result[0]['birth_date']);
    }

    function testOrderByCreatedAtDesc()
    {
        $users = new Table("users");

        $result = $this->fetchAll(
            $users->order($users['created_at']->desc())
        );

        $this->assertEquals(array(3, 2, 1), $this->pluckIds($result));
    }

    function testSelectWhereUsernamePassword()
    {
        $users = new Table("users");

        $query = $users
            ->project(s\star())
            ->where($users['username']->eq('john'))
            ->where($users['password']->eq('john1234'));

        $result = $this->fetchAll($query);
        $this->assertEquals(1, count($result));

        $user = array_pop($result);

        $this->assertEquals(1, $user['id']);
        $this->assertEquals("john", $user['username']);
    }

    function testInsert()
    {
        $users = new Table("users");

        $rowsInserted = $this->pdo->exec(
            $users->insert()->values(array(
                "id" => 4,
                "username" => "christoph",
                "password" => "christoph1234",
                "created_at" => new \DateTime("2011-06-01"),
                "display_name" => "Christoph Hochstrasser"
            ))->toSql()
        );

        $this->assertEquals(1, $rowsInserted);

        $user = $this->pdo->query(
            $users->where($users['username']->eq('christoph'))->toSql()
        )->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(5, count($user));
        $this->assertEquals("christoph", $user['username']);
        $this->assertEquals("2011-06-01 00:00:00", $user['created_at']);
    }

    function testUpdate()
    {
        $users = new Table("users");

        $rowsUpdated = $this->pdo->exec(
            $users
            ->update()
            ->where($users['id']->eq(1))
            ->set(array('password' => 'newpassword'))
            ->toSql()
        );

        $this->assertEquals(1, $rowsUpdated);

        $user = $this->pdo->query(
            $users->where($users['id']->eq(1))
        )->fetch();

        $this->assertEquals('newpassword', $user['password']);
    }

    function testDeleteById()
    {
        $users = new Table("users");
        $query = $users->delete()->where($users['id']->eq(1));

        $rowsDeleted = $this->pdo->exec(
            $query->toSql()
        );

        $this->assertEquals(1, $rowsDeleted);

        $rest = $this->fetchAll(
            $users->project(s\sql('COUNT(id)'))
        );

        $this->assertEquals(2, $rest[0]['COUNT(id)']);
    }

    function testDeleteAll()
    {
        $users = new Table("users");

        $rowsDeleted = $this->pdo->exec(
            $users->delete()->toSql()
        );

        $this->assertEquals(3, $rowsDeleted);

        $row = $this->pdo->query(
            $users->project(s\sql('COUNT(\'id\') AS count'))
        )->fetch();

        $this->assertEquals(0, $row['count']);
    }

}
