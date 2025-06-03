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

use PHPUnit\Framework\TestCase;
use SolidWorx\Toggler\Storage\EnvStorage;

class EnvStorageTest extends TestCase
{
    public function testGet(): void
    {
        $storage = new EnvStorage();

        $this->assertFalse($storage->get('baz'));

        $_ENV['baz'] = 'foo';

        $this->assertSame('foo', $storage->get('baz'));

        unset($_ENV['baz']);

        $_SERVER['baz'] = 'bar';

        $this->assertSame('bar', $storage->get('baz'));

        unset($_SERVER['baz']);

        putenv('baz=baz');

        $this->assertSame('baz', $storage->get('baz'));

        putenv('baz');

        $this->assertFalse($storage->get('baz'));

        $this->assertSame($_SERVER, $storage->all());

        $_ENV['foo'] = 'bar';

        $this->assertSame([
            'foo' => 'bar',
        ] + $_SERVER, $storage->all());

        $_SERVER['bar'] = 'baz';

        $this->assertSame([
            'foo' => 'bar',
        ] + $_SERVER, $storage->all());

        putenv('baz=baz');

        $this->assertSame([
            'foo' => 'bar',
        ] + $_SERVER + [
            'baz' => 'baz',
        ], $storage->all());

        putenv('baz');

        $this->assertSame([
            'foo' => 'bar',
        ] + $_SERVER + [
            'bar' => 'baz',
        ], $storage->all());

        unset($_ENV['foo'], $_SERVER['bar']);

        $this->assertSame($_SERVER, $storage->all());
    }
}
