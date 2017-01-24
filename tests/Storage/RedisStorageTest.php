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

use SolidWorx\Toggler\Storage\RedisStorage;

class RedisStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->createPartialMock(\Predis\Client::class, ['get', 'set']);
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
}