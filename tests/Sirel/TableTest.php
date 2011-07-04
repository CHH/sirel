<?php

namespace Sirel\Test;

use Sirel\Sirel,
    Sirel\Table;

class TableTest extends \PHPUnit_Framework_TestCase
{
    protected $users;

    function setUp()
    {
        $this->users = new Table("users");
    }

    function testOffsetGetReturnsGenericAttributeInstance()
    {
        $userId = $this->users['id'];

        $this->assertInstanceOf("\\Sirel\\Attribute\\Attribute", $userId);
        $this->assertEquals("users.id", (string) $userId);
    }

    function testAttributesCanAlsoBeAccessedAsProperties()
    {
        $userId = $this->users->id;

        $this->assertEquals($this->users['id'], $userId);
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    function testExceptionIfStrictSchemeAndUndefinedAttributeAccess()
    {
        $this->users->setStrictScheme(true);
        $this->users['foo'];
    }

    function testProject()
    {
        $users = $this->users;

        $sqlString = "SELECT users.id FROM users";
        $this->assertEquals($sqlString, $users->project($users['id'])->toSql());
    }

    function testJoin()
    {
        $users = $this->users;
        $profiles = new Table("profiles");

        $select = $users->join($profiles)
            ->on($users['id']->eq($profiles['user_id']))
            ->where($users['id']->eq(1));

        $sqlString = "SELECT * FROM users INNER JOIN profiles"
            . " ON users.id = profiles.user_id WHERE users.id = 1";

        $this->assertEquals($sqlString, $select->toSql());
    }

    function testOffset()
    {
        $query = $this->users->skip(10);

        $sqlString = "SELECT * FROM users OFFSET 10";
        $this->assertEquals($sqlString, $query->toSql());
    }

    function testLimit()
    {
        $query = $this->users->take(5);

        $sqlString = "SELECT * FROM users LIMIT 5";
        $this->assertEquals($sqlString, $query->toSql());
    }

    function testOrder()
    {
        $users = $this->users;

        $sqlString = "SELECT * FROM users ORDER BY users.username ASC";
        $this->assertEquals($sqlString, $users->order($users['username']->asc())->toSql());
        $this->assertEquals($sqlString, $users->order($users['username'])->toSql());
    }

    function testOrderDesc()
    {
        $users = $this->users;

        $sqlString = "SELECT * FROM users ORDER BY users.username DESC";
        $this->assertEquals($sqlString, $users->order($users['username']->desc())->toSql());
        $this->assertEquals($sqlString, $users->order($users['username'], \Sirel\Node\Order::DESC)->toSql());
    }

    function testGroupBy()
    {
        $users = $this->users;
        
        $query = $users->group($users['username']);

        $sqlString = "SELECT * FROM users GROUP BY users.username";
        $this->assertEquals($sqlString, $query->toSql());
    }

    function testUpdate()
    {
        $users = $this->users;
        $query = $users->update()
            ->where($users['username']->eq('christoph'))
            ->set(array('password' => 'foo'));

        $sqlString = "UPDATE users SET password = 'foo'" 
            . " WHERE users.username = 'christoph'";

        $this->assertEquals($sqlString, $query->toSql());
    }

    function testInsert()
    {
        $users = $this->users;

        $insert = $users->insert()->values(array(
            "username" => "johnny",
            "password" => "ring of fire"
        ));

        $sqlString = "INSERT INTO users (username, password)" 
            . " VALUES ('johnny', 'ring of fire')";

        $this->assertEquals($sqlString, $insert->toSql());
    }

    function testDelete()
    {
        $users = $this->users;

        $delete = $users->delete()
            ->where($users['username']->eq('johnny'))
            ->take(1);

        $sqlString = "DELETE FROM users WHERE users.username = 'johnny' LIMIT 1";

        $this->assertEquals($sqlString, $delete->toSql());
    }
}
