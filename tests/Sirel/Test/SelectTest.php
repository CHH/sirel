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
        $users = $u = $this->users;
        $query = $users
            ->where($u->username->eq('johnny'))
            ->where($u->password->eq('superSecretPass'));

        $sqlString = "SELECT * FROM users WHERE users.username = 'johnny'"
            . " AND users.password = 'superSecretPass';";

        $this->assertEquals($sqlString, $query->toSql());

        $query = $users->where(
            $u->username->eq('johnny'),
            $u->password->eq('superSecretPass')
        );

        $this->assertEquals($sqlString, $query->toSql());
    }

    function testSelectDistinct()
    {
        $users = $this->users;
        $select = $users->from()->project($users['username'])->distinct();

        $sqlString = "SELECT DISTINCT users.username FROM users;";

        $this->assertEquals($sqlString, $select->toSql());
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
        $users = $u = $this->users;
        $query = $users->where(
            $u['username']->eq('johnny')
            ->_or($u['username']->eq('tom'))
        );

        $sqlString = "SELECT * FROM users WHERE (users.username = 'johnny'"
            . " OR users.username = 'tom');";
        $this->assertEquals($sqlString, $query->toSql());
    }

    function testLeftJoin()
    {
        $users = $u = $this->users;
        $profiles = $p = new Table("profiles");

        $select = $users->project(Sirel::star())
            ->leftJoin($profiles)->on($u['id']->eq($p['user_id']))
            ->where($u['id']->eq(1));

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

    function testCompileDeleteFromSelect()
    {
        $users = $u = $this->users;
        $select = $users->take(1)->where($u->username->eq("christoph"));
        $delete = $select->compileDelete();

        $sqlString = "DELETE FROM users WHERE users.username = 'christoph' LIMIT 1;";
        $this->assertEquals($sqlString, $delete->toSql());
    }

    function testNot()
    {
        $users = $this->users;

        $select = $users->where($users->username->not());
        $sqlString = "SELECT * FROM users WHERE NOT(users.username);";

        $this->assertEquals($sqlString, $select->toSql());
    }

    function testReverseOrder()
    {
        $users = $u = $this->users;

        $select = $users->order($u->username->desc());
        $select->reverseOrder();
        $sqlString = "SELECT * FROM users ORDER BY users.username ASC;";

        $this->assertEquals($sqlString, $select->toSql());
    }

    function testReverseOrderOnlyOnSelectedField()
    {
        $users = $u = $this->users;

        $select = $users->order($u->username->desc())
            ->order($u->id->asc());

        $select->reverseOrder([$u->id]);
        $sqlString = "SELECT * FROM users ORDER BY users.username DESC, users.id DESC;";

        $this->assertEquals($sqlString, $select->toSql());
    }

    function testReorder()
    {
        $users = $u = $this->users;

        $select = $users->order($u->username->desc());
        $select->reorder($users->id->desc());

        $sqlString = "SELECT * FROM users ORDER BY users.id DESC;";

        $this->assertEquals($sqlString, $select->toSql());
    }

    function testCount()
    {
        $users = $u = $this->users;

        $select = $users->project($u->id->count());
        $sqlString = "SELECT COUNT(users.id) FROM users;";

        $this->assertEquals($sqlString, $select->toSql());
    }

    function testSum()
    {
        $users = $u = $this->users;

        $select = $users->project($u->pets->sum());
        $sqlString = "SELECT SUM(users.pets) FROM users;";

        $this->assertEquals($sqlString, $select->toSql());
    }
}
