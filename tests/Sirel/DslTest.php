<?php

namespace Sirel\Test;

use Sirel\Table;

class DslTest extends \PHPUnit_Framework_TestCase
{
    protected $users;

    function setUp()
    {
        $this->users = new Table("users");
    }

    function testSimpleSelect()
    {
        $users = $this->users;
        $query = $users
            ->where($users['username']->eq('johnny'))
            ->where($users['password']->eq('superSecretPass'));

        $sqlString = "SELECT * FROM users WHERE users.username = 'johnny'"
            . " AND users.password = 'superSecretPass'";

        $this->assertEquals($sqlString, $query->toSql());

        $query = $users->where(
            $users['username']->eq('johnny'),
            $users['password']->eq('superSecretPass')
        );

        $this->assertEquals($sqlString, $query->toSql());
    }

    function testSimpleGroup()
    {
        $users = $this->users;
        
        $query = $users->group($users['username']);

        $sqlString = "SELECT * FROM users GROUP BY users.username";
        $this->assertEquals($sqlString, $query->toSql());
    }

    function testSimpleOr()
    {
        $users = $this->users;
        $query = $users->where(
            $users['username']->eq('johnny')
            ->_or($users['username']->eq('tom'))
        );

        $sqlString = "SELECT * FROM users WHERE (users.username = 'johnny'"
            . " OR users.username = 'tom')";
        $this->assertEquals($sqlString, $query->toSql());
    }

    function testSimpleOrder()
    {
        $users = $this->users;

        $sqlString = "SELECT * FROM users ORDER BY users.username ASC";
        $this->assertEquals($sqlString, $users->order($users['username']->asc())->toSql());
        $this->assertEquals($sqlString, $users->order($users['username'])->toSql());
    }

    function testSimpleOrderDesc()
    {
        $users = $this->users;

        $sqlString = "SELECT * FROM users ORDER BY users.username DESC";
        $this->assertEquals($sqlString, $users->order($users['username']->desc())->toSql());
        $this->assertEquals($sqlString, $users->order($users['username'], \Sirel\Node\Order::DESC)->toSql());
    }

    function testSimpleProject()
    {
        $users = $this->users;

        $sqlString = "SELECT users.id FROM users";
        $this->assertEquals($sqlString, $users->project($users['id'])->toSql());
    }

    function testSimpleSelectLimit()
    {
        $query = $this->users->take(5);

        $sqlString = "SELECT * FROM users LIMIT 5";
        $this->assertEquals($sqlString, $query->toSql());
    }

    function testSimpleSelectOffset()
    {
        $query = $this->users->skip(10);

        $sqlString = "SELECT * FROM users OFFSET 10";
        $this->assertEquals($sqlString, $query->toSql());
    }
}
