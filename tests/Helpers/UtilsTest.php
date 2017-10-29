<?php

namespace Crust\Tests\Helpers;

use Crust\Helpers\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{

    public function testIsAssoc()
    {
        $assocArr = ['foo' => 'bar', 'some', 'var'];
        $seqArr = ['foo', 'bar', 'some', 'var'];
        $this->assertTrue(Utils::isAssoc($assocArr));
        $this->assertFalse(Utils::isAssoc($seqArr));
    }

    public function testJoin()
    {
        $arr1 = ['foo' => 'bar', 'some' => 'var', 'one', 'two'];
        $arr2 = ['two', 'three', 'some' => 'another_var', 'four'];
        $this->assertEquals(['foo' => 'bar', 'some' => 'another_var', 'one', 'two', 'three', 'four'], Utils::join($arr1, $arr2));

        $arr1 = ['one', 'two', 'three'];
        $arr2 = ['two', 'four', 'six'];
        $this->assertEquals(['one', 'two', 'three', 'four', 'six'], Utils::join($arr1, $arr2));
    }
}
