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
            'foobar' => false,
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
            'foobar' => false,
        ];

        $this->assertSame($features, $this->readAttribute(Config::instance(), 'config'));
    }

    public function testToggleConfigWithYamlFile()
    {
        toggleConfig(__DIR__.'/stubs/config.yml');

        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $this->assertSame($features, $this->readAttribute(Config::instance(), 'config'));
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
            'foobar' => false,
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
            'foobar' => false,
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
            'foobar' => false,
        ];

        toggleConfig($features);

        $this->assertSame('abcdef', toggle('baz', function () {}, $callback));
        $this->assertNull(toggle('baz', function () {}));
    }

    public function testToggleCallback()
    {
        $features = [
            'foo' => function (array $data) {
                return $data['value'] === 123;
            },
            'bar' => function ($a, $b) {
                return ($a + $b) === 10;
            },
        ];

        toggleConfig($features);

        // Call all these function twice to check that it is memoized correctly
        $this->assertTrue(toggle('foo', [['value' => 123]]));
        $this->assertTrue(toggle('foo', [['value' => 123]]));
        $this->assertFalse(toggle('foo', [['value' => 456]]));
        $this->assertFalse(toggle('foo', [['value' => 456]]));

        $this->assertTrue(toggle('bar', [5, 5]));
        $this->assertTrue(toggle('bar', [5, 5]));
        $this->assertFalse(toggle('bar', [1, 2]));
        $this->assertFalse(toggle('bar', [1, 2]));
    }

    protected function tearDown()
    {
        Config::instance()->clear();
    }
}