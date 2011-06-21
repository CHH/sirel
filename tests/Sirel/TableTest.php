<?php

namespace Sirel\Test;

use Sirel\Table;

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
}
