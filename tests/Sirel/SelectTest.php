<?php

namespace Sirel\Test;

use Sirel\Sirel,
    Sirel\Table;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    protected $users;

    function setUp()
    {
        $this->users = new Table("users");
    }

    function testSelect()
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

    function testOr()
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

    function testLeftJoin()
    {
        $users = $this->users;
        $profiles = new Table("profiles");

        $select = $users->project(Sirel::star())
            ->leftJoin($profiles)->on($users['id']->eq($profiles['user_id']))
            ->where($users['id']->eq(1));

        $sqlString = "SELECT * FROM users LEFT JOIN profiles"
            . " ON users.id = profiles.user_id WHERE users.id = 1";

        $this->assertEquals($sqlString, $select->toSql());
    }

    function testInnerJoin()
    {
        $users = $this->users;
        $profiles = new Table("profiles");

        $select = $users->project(Sirel::star())
            ->join($profiles)->on($users['id']->eq($profiles['user_id']))
            ->where($users['id']->eq(1));

        $sqlString = "SELECT * FROM users INNER JOIN profiles" 
            . " ON users.id = profiles.user_id WHERE users.id = 1";

        $this->assertEquals($sqlString, $select->toSql());
    }

    function testCompileUpdateFromSelect()
    {
        $users = $this->users;
        $update = $users->where($users['username']->eq('christoph'))->take(1)
            ->compileUpdate()->set(array('password' => 'updated'));

        $sqlString = "UPDATE users SET password = 'updated'"
            . " WHERE users.username = 'christoph' LIMIT 1";

        $this->assertEquals($sqlString, $update->toSql());
    }
}
