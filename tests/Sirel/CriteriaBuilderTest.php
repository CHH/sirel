<?php

namespace Sirel\Test;

use InvalidArgumentException,
    Sirel\CriteriaBuilder,
    Sirel\Criterion\Equals;

class CriteriaBuilderTest extends \PHPUnit_Framework_TestCase
{
    protected $builder;

    function setUp()
    {
        $this->builder = new CriteriaBuilder;
    }

    function testCanBeInitializedWithClosure()
    {
        $builder = new CriteriaBuilder(function($c) {
            $c->eq("foo", "bar");
            $c->eq("bar", "baz");
        });

        $this->assertEquals(2, count($builder));
    }

    function testCanAddCriterions()
    {
        $equals = new Equals("foo", "bar");
        $this->builder->add($equals);

        $this->assertSame($equals, $this->builder[0], "add() can be used to add criterions");

        $this->builder[] = $equals;
        $this->assertSame($equals, $this->builder[1], "Criterions can also be appended with\
            the Offset Operator");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testOverridesOffsetSetToAllowOnlyCriterionsToBeSet()
    {
        $foo = new \StdClass;
        $this->builder[] = $foo;
    }

    function testExposesAllBuilder()
    {
        $this->builder->all()
            ->eq("username", "foo")
            ->eq("password", "mySecretPassword");

        $this->assertInstanceOf("\\Sirel\\Criterion\\All", $this->builder[0]);
        $this->assertEquals(2, count($this->builder[0]));
    }

    function testExposesAnyBuilder()
    {
        $this->builder->any()
            ->eq("username", "foo")
            ->eq("username", "bar");

        $this->assertInstanceOf("\\Sirel\\Criterion\\Any", $this->builder[0]);
        $this->assertEquals(2, count($this->builder[0]));
    }

    function testGt()
    {
        $this->builder->gt("foo", 10);
        $this->assertInstanceOf("\\Sirel\\Criterion\\GreaterThan", $this->builder[0]);
    }

    function testGte()
    {
        $this->builder->gte("foo", 10);
        $this->assertInstanceOf("\\Sirel\\Criterion\\GreaterThanEquals", $this->builder[0]);
    }

    function testLt()
    {
        $this->builder->lt("foo", 10);
        $this->assertInstanceOf("\\Sirel\\Criterion\\LessThan", $this->builder[0]);
    }

    function testLte()
    {
        $this->builder->lte("foo", 10);
        $this->assertInstanceOf("\\Sirel\\Criterion\\LessThanEquals", $this->builder[0]);
    }

    function testIn()
    {
        $this->builder->in("foo", array("bar", "baz"));
        $this->assertEquals(array("bar", "baz"), $this->builder[0]->getExpected());
    }

    function testTake()
    {
        $this->builder->take(5);
        $this->assertEquals(5, $this->builder[0]->getNumber());
    }
}
