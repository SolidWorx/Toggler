<?php

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace Tests\Toggler;

use Toggler\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $instance1 = Config::instance();
        $instance2 = Config::instance();

        $this->assertSame($instance1, $instance2);
    }

    public function testSetConfig()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false
        ];

        $instance = Config::instance();

        $instance->setConfig($features);

        $this->assertSame($features, $this->readAttribute($instance, 'config'));
    }

    public function testGet()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false
        ];

        $instance = Config::instance();

        $instance->setConfig($features);

        $this->assertTrue($instance->get('foo'));
        $this->assertTrue($instance->get('bar'));
        $this->assertFalse($instance->get('baz'));
        $this->assertFalse($instance->get('foobar'));
    }

    public function testGetException()
    {
        $this->setExpectedException('InvalidArgumentException', 'The config "non-foobar" does not exist');
        $instance = Config::instance();

        $instance->get('non-foobar');
    }

    public function testClear()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false
        ];

        $instance = Config::instance();

        $instance->setConfig($features);

        $this->assertSame($features, $this->readAttribute($instance, 'config'));

        $instance->clear();

        $this->assertSame([], $this->readAttribute($instance, 'config'));
    }

    protected function tearDown()
    {
        Config::instance()->clear();
    }

}
