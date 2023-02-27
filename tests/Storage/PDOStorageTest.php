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

use PHPUnit\Framework\TestCase;
use SolidWorx\Toggler\Storage\PdoStorage;

class PDOStorageTest extends TestCase
{
    public function testGet(): void
    {
        $storage = new PdoStorage('sqlite::memory:');

        self::assertFalse($storage->get('foobar'));
    }

    public function testSet(): void
    {
        $storage = new PdoStorage('sqlite::memory:');

        $storage->set('foo', true);
        $storage->set('bar', false);

        self::assertTrue($storage->get('foo'));
        self::assertFalse($storage->get('bar'));
    }

    public function testUpdateExistingValue(): void
    {
        $storage = new PdoStorage('sqlite::memory:');

        $storage->set('foo', true);

        self::assertTrue($storage->get('foo'));

        $storage->set('foo', false);
        self::assertFalse($storage->get('foo'));
    }

    public function testGetAll(): void
    {
        $storage = new PdoStorage('sqlite::memory:');

        $storage->set('foo', true);
        $storage->set('bar', false);

        self::assertEquals(['bar', 'foo'], $storage->all());
    }
}
