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

namespace SolidWorx\Toggler\Tests\Storage;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Version as PHPUnitVersion;
use Predis\Client;
use SolidWorx\Toggler\Storage\RedisStorage;

class RedisStorageTest extends TestCase
{
    /**
     * @var MockObject&Client<string>
     */
    private $redis;

    public function setUp(): void
    {
        $mockBuilder = $this->getMockBuilder(Client::class);

        if (\version_compare(PHPUnitVersion::series(), '8.3', '>=')) {
            $this->redis = $mockBuilder
                ->addMethods(['get', 'set'])
                ->getMock();
        } else {
            // @phpstan-ignore-next-line
            $this->redis = $mockBuilder
                ->setMethods(['get', 'set'])
                ->getMock();
        }
    }

    public function testConstructorException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('SolidWorx\Toggler\Storage\RedisStorage::__construct() expects parameter 1 to be Redis, RedisArray, RedisCluster or Predis\Client, NULL given');

        new RedisStorage(null);
    }

    public function testGet(): void
    {
        $this->redis->expects(self::at(0))
            ->method('get')
            ->with('foobar')
            ->willReturn(true);

        $storage = new RedisStorage($this->redis);

        self::assertTrue($storage->get('foobar'));
        self::assertNull($storage->get('baz'));
    }

    public function testSet(): void
    {
        $this->redis->expects(self::at(0))
            ->method('set')
            ->with('foobar', false);

        $storage = new RedisStorage($this->redis);

        $storage->set('foobar', false);
    }

    public function testGetWithNamespace(): void
    {
        $namespace = 'fooNamespace';
        $this->redis->expects(self::at(0))
            ->method('get')
            ->with($namespace.':foobar')
            ->willReturn(true);

        $this->redis->expects(self::at(1))
            ->method('get')
            ->with($namespace.':baz');

        $storage = new RedisStorage($this->redis, $namespace);

        self::assertTrue($storage->get('foobar'));
        self::assertNull($storage->get('baz'));
    }

    public function testSetWithNamespace(): void
    {
        $namespace = 'fooNamespace';
        $this->redis->expects(self::at(0))
            ->method('set')
            ->with($namespace.':foobar', false);

        $this->redis->expects(self::at(1))
            ->method('get')
            ->with($namespace.':foobar')
            ->willReturn(false);

        $storage = new RedisStorage($this->redis, $namespace);

        $storage->set('foobar', false);

        self::assertFalse($storage->get('foobar'));
    }
}
