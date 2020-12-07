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

namespace SolidWorx\Toggler\Tests;

use PHPUnit\Framework\TestCase;
use SolidWorx\Toggler\Storage\StorageFactory;
use SolidWorx\Toggler\Toggle;
use Symfony\Component\ExpressionLanguage\Expression;

class ToggleTest extends TestCase
{
    public function testIsActive(): void
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $instance = new Toggle(StorageFactory::factory($features));

        self::assertTrue($instance->isActive('foo'));
        self::assertTrue($instance->isActive('bar'));
        self::assertFalse($instance->isActive('baz'));
        self::assertFalse($instance->isActive('foobar'));
    }

    public function testIsActiveTruthy(): void
    {
        $features = [
            'foo' => true,
            'bar' => 1,
            'baz' => '1',
            'foobar' => 'on',
        ];

        $instance = new Toggle(StorageFactory::factory($features));

        self::assertTrue($instance->isActive('foo'));
        self::assertTrue($instance->isActive('bar'));
        self::assertTrue($instance->isActive('baz'));
        self::assertTrue($instance->isActive('foobar'));
    }

    public function testIsActiveFalsey(): void
    {
        $features = [
            'foo' => false,
            'bar' => 0,
            'baz' => '0',
            'foobar' => 'off',
            'foobaz' => [],
            'bazbar' => new \StdClass(),
        ];

        $instance = new Toggle(StorageFactory::factory($features));

        self::assertFalse($instance->isActive('foo'));
        self::assertFalse($instance->isActive('bar'));
        self::assertFalse($instance->isActive('baz'));
        self::assertFalse($instance->isActive('foobar'));
        self::assertFalse($instance->isActive('foobaz'));
        self::assertFalse($instance->isActive('bazbar'));
    }

    public function testIsActiveCallback(): void
    {
        $features = [
            'foo' => function (array $data): bool {
                return 123 === $data['value'];
            },
            'bar' => function ($a, $b): bool {
                return ($a + $b) === 10;
            },
        ];

        $instance = new Toggle(StorageFactory::factory($features));

        // Call all these function twice to check that it is memoized correctly
        self::assertTrue($instance->isActive('foo', [['value' => 123]]));
        self::assertTrue($instance->isActive('foo', [['value' => 123]]));
        self::assertFalse($instance->isActive('foo', [['value' => 456]]));
        self::assertFalse($instance->isActive('foo', [['value' => 456]]));

        self::assertTrue($instance->isActive('bar', [5, 5]));
        self::assertTrue($instance->isActive('bar', [5, 5]));
        self::assertFalse($instance->isActive('bar', [1, 2]));
        self::assertFalse($instance->isActive('bar', [1, 2]));
    }

    public function testIsActiveExpression(): void
    {
        $features = [
            'foo' => new Expression('newValue > 10 and some["value"] < 10'),
        ];

        $instance = new Toggle(StorageFactory::factory($features));

        self::assertTrue($instance->isActive('foo', ['newValue' => 123, 'some' => ['value' => 5]]));
        self::assertFalse($instance->isActive('foo', ['newValue' => 123, 'some' => ['value' => 500]]));
    }

    public function testIsActiveStringClass(): void
    {
        $features = [
            'foo' => new class { public function __toString() { return '1'; }},
            'bar' => new class { public function __toString() { return '0'; }},
        ];

        $instance = new Toggle(StorageFactory::factory($features));

        self::assertTrue($instance->isActive('foo'));
        self::assertFalse($instance->isActive('bar'));
    }
}
