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
            . " AND users.password = 'superSecretPass';";

        $this->assertEquals($sqlString, $query->toSql());

        $query = $users->where(
            $users['username']->eq('johnny'),
            $users['password']->eq('superSecretPass')
        );

        $this->assertEquals($sqlString, $query->toSql());
    }

    function testPassProjectionsAsMultipleArguments()
    {
        $users = $this->users;
        $select = $users->from()->project($users['id'], $users['username']);

        $sqlString = "SELECT users.id, users.username FROM users;";

        $this->assertEquals($sqlString, $select->toSql());
    }

    function testOr()
    {
        $users = $this->users;
        $query = $users->where(
            $users['username']->eq('johnny')
            ->_or($users['username']->eq('tom'))
        );

        $sqlString = "SELECT * FROM users WHERE (users.username = 'johnny'"
            . " OR users.username = 'tom');";
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
            . " ON users.id = profiles.user_id WHERE users.id = 1;";

        $this->assertEquals($sqlString, $select->toSql());
    }

    function testPassConstraintToJoin()
    {
        $users = $this->users;
        $profiles = new Table("profiles");

        $select = $users->project(Sirel::star())
            ->join($profiles, $users['id']->eq($profiles['user_id']));

        $sqlString = "SELECT * FROM users INNER JOIN profiles"
            . " ON users.id = profiles.user_id;";

        $this->assertEquals($sqlString, $select->toSql());
    }

    function testRepeatedCallToOnOverridesJoinConstraint()
    {
        $users = $this->users;
        $profiles = new Table("profiles");

        $select = $users->project(Sirel::star())
            ->join($profiles, $users['id']->eq($profiles['user_id']));

        $sqlString = "SELECT * FROM users INNER JOIN profiles"
            . " ON users.id = profiles.user_id;";

        $this->assertEquals($sqlString, $select->toSql());

        $select->on($profiles['user_id']->eq($users['id']));

        $this->assertEquals(
            "SELECT * FROM users INNER JOIN profiles" 
            . " ON profiles.user_id = users.id;",
            $select->toSql()
        );
    }

    function testInnerJoin()
    {
        $users = $this->users;
        $profiles = new Table("profiles");

        $select = $users->project(Sirel::star())
            ->join($profiles)->on($users['id']->eq($profiles['user_id']));

        $sqlString = "SELECT * FROM users INNER JOIN profiles" 
            . " ON users.id = profiles.user_id;";

        $this->assertEquals($sqlString, $select->toSql());

        $this->assertEquals(
            $sqlString, 
            $users
                ->project(Sirel::star())
                ->innerJoin($profiles, $users['id']->eq($profiles['user_id']))
                ->toSql()
        );
    }

    function testNaturalInnerJoin()
    {
        $users = $this->users;
        $profiles = new Table("profiles");

        $select = $users->project(Sirel::star())
            ->innerJoin($profiles)->natural();

        $sqlString = "SELECT * FROM users NATURAL INNER JOIN profiles;"; 

        $this->assertEquals($sqlString, $select->toSql());
    }

    function testCompileUpdateFromSelect()
    {
        $users = $this->users;
        $update = $users->where($users['username']->eq('christoph'))->take(1)
            ->compileUpdate()->set(array('password' => 'updated'));

        $sqlString = "UPDATE users SET password = 'updated'"
            . " WHERE users.username = 'christoph' LIMIT 1;";

        $this->assertEquals($sqlString, $update->toSql());
    }

    function testNot()
    {
        $users = $this->users;

        $select = $users->where($users->not($users->username));
        $sqlString = "SELECT * FROM users WHERE NOT(users.username);";

        $this->assertEquals($sqlString, $select->toSql());
    }
}
