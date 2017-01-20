<?php

declare(strict_types=1);

/*
 * This file is part of the Toggler package.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SolidWorx\Tests\Toggler;

use SolidWorx\Toggler\Config;
use SolidWorx\Toggler\Storage\ArrayStorage;
use SolidWorx\Toggler\Storage\StorageInterface;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testSetConfig()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $config = new Config($features);

        $this->assertEquals(new ArrayStorage($features), $this->readAttribute($config, 'config'));
    }

    public function testGet()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $config = new Config($features);

        $this->assertTrue($config->get('foo'));
        $this->assertTrue($config->get('bar'));
        $this->assertFalse($config->get('baz'));
        $this->assertFalse($config->get('foobar'));
    }

    public function testStorage()
    {
        toggleConfig(new Storage());

        $this->assertTrue(toggle('foo'));
        $this->assertFalse(toggle('bar'));
        $this->assertFalse(toggle('baz'));
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

    public function set(string $key, bool $value): bool
    {
        // noop
    }
}
