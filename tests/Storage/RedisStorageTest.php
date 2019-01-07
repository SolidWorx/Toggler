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

namespace SolidWorx\Tests\Toggler\Storage;

use PHPUnit\Framework\TestCase;
use SolidWorx\Toggler\Storage\RedisStorage;

class RedisStorageTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->createPartialMock(\Predis\Client::class, ['get', 'set']);
    }

    public function testConstructorException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('SolidWorx\Toggler\Storage\RedisStorage::__construct() expects parameter 1 to be Redis, RedisArray, RedisCluster or Predis\Client, NULL given');

        $storage = new RedisStorage(null);
    }

    public function testGet()
    {
        $this->redis->expects($this->at(0))
            ->method('get')
            ->with('foobar')
            ->willReturn(true);

        $storage = new RedisStorage($this->redis);

        $this->assertTrue($storage->get('foobar'));
        $this->assertNull($storage->get('baz'));
    }

    public function testSet()
    {
        $this->redis->expects($this->at(0))
            ->method('set')
            ->with('foobar', false);

        $storage = new RedisStorage($this->redis);

        $storage->set('foobar', false);
    }

    public function testGetWithNamespace()
    {
        $namespace = 'fooNamespace';
        $this->redis->expects($this->at(0))
            ->method('get')
            ->with($namespace . ':foobar')
            ->willReturn(true);

        $this->redis->expects($this->at(1))
            ->method('get')
            ->with($namespace . ':baz');

        $storage = new RedisStorage($this->redis, $namespace);

        $this->assertTrue($storage->get('foobar'));
        $this->assertNull($storage->get('baz'));
    }

    public function testSetWithNamespace()
    {
        $namespace = 'fooNamespace';
        $this->redis->expects($this->at(0))
            ->method('set')
            ->with($namespace . ':foobar', false);

        $storage = new RedisStorage($this->redis, $namespace);

        $storage->set('foobar', false);
    }
}
