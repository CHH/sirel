<?php

namespace Sirel\Test;

use Sirel\Sirel,
    Sirel\Table;

class UpdateTest extends \PHPUnit_Framework_TestCase
{
    protected $users;

    function setUp()
    {
        $this->users = new Table("users");
    }

    function testWhere()
    {
        $users = $u = $this->users;
        $update = $users->update()->where($u->id->eq(1))->set(['foo' => 'bar']);

        $sqlString = "UPDATE users SET foo = 'bar' WHERE users.id = 1;";
        $this->assertEquals($sqlString, $update->toSql());
    }

    function testLimit()
    {
        $users = $u = $this->users;
        $update = $users->update()->take(1)->where($u->id->eq(1))->set(['foo' => 'bar']);

        $sqlString = "UPDATE users SET foo = 'bar' WHERE users.id = 1 LIMIT 1;";
        $this->assertEquals($sqlString, $update->toSql());
    }
}
