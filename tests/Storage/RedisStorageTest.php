<?php

declare(strict_types=1);

/*
 * This file is part of SolidWorx Toggler project.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SolidWorx\Toggler\Tests\Storage;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use SolidWorx\Toggler\Storage\RedisStorage;

class RedisStorageTest extends TestCase
{
    /**
     * @var MockObject&Client
     */
    private MockObject $redis;

    protected function setUp(): void
    {
        $mockBuilder = $this->getMockBuilder(Client::class);

        $this->redis = $mockBuilder
            ->addMethods(['get', 'set', 'keys'])
            ->getMock();
    }

    public function testConstructorException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(RedisStorage::class . '::__construct() expects parameter 1 to be Redis, RedisArray, RedisCluster or Predis\Client, NULL given');

        new RedisStorage(null);
    }

    public function testGet(): void
    {
        $this->redis
            ->expects(self::exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls(true, null);

        $storage = new RedisStorage($this->redis);

        $this->assertTrue($storage->get('foobar'));
        $this->assertNull($storage->get('baz'));
    }

    public function testSet(): void
    {
        $this->redis->expects(self::once())
            ->method('set')
            ->with('foobar', false);

        $storage = new RedisStorage($this->redis);

        $storage->set('foobar', false);
    }

    public function testGetWithNamespace(): void
    {
        $namespace = 'fooNamespace';
        $this->redis
            ->expects(self::exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls(true, null);

        $storage = new RedisStorage($this->redis, $namespace);

        $this->assertTrue($storage->get('foobar'));
        $this->assertNull($storage->get('baz'));
    }

    public function testSetWithNamespace(): void
    {
        $namespace = 'fooNamespace';
        $this->redis->expects(self::once())
            ->method('set')
            ->with($namespace . ':foobar', false);

        $this->redis->expects(self::once())
            ->method('get')
            ->with($namespace . ':foobar')
            ->willReturn(false);

        $storage = new RedisStorage($this->redis, $namespace);

        $storage->set('foobar', false);

        $this->assertFalse($storage->get('foobar'));
    }

    public function testAll(): void
    {
        $namespace = 'fooNamespace';

        $this->redis->expects(self::once())
            ->method('keys')
            ->with($namespace . ':*')
            ->willReturn([$namespace . ':foo', $namespace . ':bar', $namespace . ':baz']);

        $storage = new RedisStorage($this->redis, $namespace);

        $storage->set('foobar', false);

        $this->assertSame(['foo', 'bar', 'baz'], $storage->all());
    }
}
