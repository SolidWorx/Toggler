<?php

declare(strict_types=1);

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace SolidWorx\Tests\Toggler;

use SolidWorx\Toggler\Config;
use SolidWorx\Toggler\Storage\StorageInterface;

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
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The config "non-foobar" does not exist');
        $instance = Config::instance();

        $instance->get('non-foobar');
    }

    public function testStorage()
    {
        toggleConfig(new Storage());

        $this->assertTrue(toggle('foo'));
        $this->assertFalse(toggle('bar'));
        $this->assertFalse(toggle('baz'));
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

        $this->assertSame(null, $this->readAttribute($instance, 'config'));
    }

    protected function tearDown()
    {
        Config::instance()->clear();
    }
}

class Storage implements StorageInterface
{
    public function get(string $key): bool
    {
        if ($key === 'foo') {
            return true;
        }

        return false;
    }
}
