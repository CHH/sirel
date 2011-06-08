<?php

namespace Sirel\Test;

use Sirel\Table;

class DslTest extends \PHPUnit_Framework_TestCase
{
    function testSimpleSelect()
    {
        $users = new Table("users");
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

    function testSimpleSelectLimit()
    {
        $users = new Table("users");
        $query = $users->take(5);

        $sqlString = "SELECT * FROM users LIMIT 5";
        $this->assertEquals($sqlString, $query->toSql());
    }

    function testSimpleSelectOffset()
    {
        $users = new Table("users");
        $query = $users->skip(10);

        $sqlString = "SELECT * FROM users OFFSET 10";
        $this->assertEquals($sqlString, $query->toSql());
    }
}
