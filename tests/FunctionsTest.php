<?php

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace Tests\Toggler;

use Toggler\Config;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testToggleConfig()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false
        ];

        toggleConfig($features);

        $this->assertSame($features, $this->readAttribute(Config::instance(), 'config'));
    }

    public function testToggleConfigWithFile()
    {
        toggleConfig(__DIR__.'/stubs/config.php');

        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false
        ];

        $this->assertSame($features, $this->readAttribute(Config::instance(), 'config'));
    }

    /**
     * @dataProvider truthyData
     */
    public function testIsTruthy($value)
    {
        $this->assertTrue(isTruthy($value));
    }

    /**
     * @dataProvider falseyData
     */
    public function testIsTruthyFalse($value)
    {
        $this->assertFalse(isTruthy($value));
    }

    public function testToggle()
    {
        $callback = function () {
            return 'abcdef';
        };

        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false
        ];

        toggleConfig($features);

        $this->assertSame('abcdef', toggle('foo', $callback));
    }

    public function testToggleReturn()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false
        ];

        toggleConfig($features);

        $this->assertTrue(toggle('foo'));
        $this->assertTrue(toggle('bar'));
        $this->assertFalse(toggle('baz'));
        $this->assertFalse(toggle('foobar'));
    }

    public function testToggleFail()
    {
        $callback = function () {
            return 'abcdef';
        };

        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false
        ];

        toggleConfig($features);

        $this->assertSame('abcdef', toggle('baz', function (){}, $callback));
        $this->assertNull(toggle('baz', function (){}));
    }

    public function truthyData()
    {
        return [
            [true],
            [1],
            ['1'],
            ['on'],
        ];
    }

    public function falseyData()
    {
        return [
            [false],
            [0],
            ['0'],
            ['off'],
            [[]],
            [new \StdClass],
        ];
    }

    protected function tearDown()
    {
        Config::instance()->clear();
    }
}