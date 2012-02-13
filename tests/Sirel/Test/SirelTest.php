<?php

namespace Sirel\Test;

use Sirel\Sirel;

class SirelTest extends \PHPUnit_Framework_TestCase
{
    function testStar()
    {
        $star = Sirel::star();

        $this->assertInstanceOf('\\Sirel\\Node\\SqlLiteral', $star);
        $this->assertEquals('*', $star->expression);
    }

    function testSqlLiteral()
    {
        $sqlLiteral = Sirel::sql('COUNT(id)');

        $this->assertInstanceOf('\\Sirel\\Node\\SqlLiteral', $sqlLiteral);
        $this->assertEquals('COUNT(id)', $sqlLiteral->expression);
    }

    function testCreateSelect()
    {
        $this->assertInstanceOf("\\Sirel\\SelectManager", Sirel::createSelect());
    }

    function testCreateInsert()
    {
        $this->assertInstanceOf("\\Sirel\\InsertManager", Sirel::createInsert());
    }

    function testCreateUpdate()
    {
        $this->assertInstanceOf("\\Sirel\\UpdateManager", Sirel::createUpdate());
    }

    function testCreateDelete()
    {
        $this->assertInstanceOf("\\Sirel\\DeleteManager", Sirel::createDelete());
    }
}
