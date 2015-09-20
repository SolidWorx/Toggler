<?php

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace Tests\Toggler;

use Toggler\Config;
use Toggler\Toggle;

class ToggleTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $instance1 = Toggle::instance();
        $instance2 = Toggle::instance();

        $this->assertSame($instance1, $instance2);
    }

    public function testIsActive()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false
        ];

        $config = Config::instance();

        $config->setConfig($features);

        $instance = Toggle::instance();

        $this->assertTrue($instance->isActive('foo'));
        $this->assertTrue($instance->isActive('bar'));
        $this->assertFalse($instance->isActive('baz'));
        $this->assertFalse($instance->isActive('foobar'));
    }

    public function testIsActiveTruthy()
    {
        $features = [
            'foo' => true,
            'bar' => 1,
            'baz' => '1',
            'foobar' => 'on'
        ];

        $config = Config::instance();

        $config->setConfig($features);

        $instance = Toggle::instance();

        $this->assertTrue($instance->isActive('foo'));
        $this->assertTrue($instance->isActive('bar'));
        $this->assertTrue($instance->isActive('baz'));
        $this->assertTrue($instance->isActive('foobar'));
    }

    public function testIsActiveFalsey()
    {
        $features = [
            'foo' => false,
            'bar' => 0,
            'baz' => '0',
            'foobar' => 'off',
            'foobaz' => [],
            'bazbar' => new \StdClass,
        ];

        $config = Config::instance();

        $config->setConfig($features);

        $instance = Toggle::instance();

        $this->assertFalse($instance->isActive('foo'));
        $this->assertFalse($instance->isActive('bar'));
        $this->assertFalse($instance->isActive('baz'));
        $this->assertFalse($instance->isActive('foobar'));
        $this->assertFalse($instance->isActive('foobaz'));
        $this->assertFalse($instance->isActive('bazbar'));
    }

    public function testExecute()
    {
        $instance = Toggle::instance();

        $this->assertSame(456, $instance->execute(function () {
            return 456;
        }));
    }

    public function testExecuteException()
    {
        $this->setExpectedException('Exception', 'Feature is not available');

        $instance = Toggle::instance();

        $instance->execute(function () {
            throw new \Exception('Feature is not available');
        });
    }

    protected function tearDown()
    {
        Config::instance()->clear();
    }
}
