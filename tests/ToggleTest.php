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

namespace SolidWorx\Toggler\Tests;

use PHPUnit\Framework\TestCase;
use SolidWorx\Toggler\Storage\StorageFactory;
use SolidWorx\Toggler\Toggle;
use stdClass;
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

        $this->assertTrue($instance->isActive('foo'));
        $this->assertTrue($instance->isActive('bar'));
        $this->assertFalse($instance->isActive('baz'));
        $this->assertFalse($instance->isActive('foobar'));
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

        $this->assertTrue($instance->isActive('foo'));
        $this->assertTrue($instance->isActive('bar'));
        $this->assertTrue($instance->isActive('baz'));
        $this->assertTrue($instance->isActive('foobar'));
    }

    public function testIsActiveFalsey(): void
    {
        $features = [
            'foo' => false,
            'bar' => 0,
            'baz' => '0',
            'foobar' => 'off',
            'foobaz' => [],
            'bazbar' => new stdClass(),
        ];

        $instance = new Toggle(StorageFactory::factory($features));

        $this->assertFalse($instance->isActive('foo'));
        $this->assertFalse($instance->isActive('bar'));
        $this->assertFalse($instance->isActive('baz'));
        $this->assertFalse($instance->isActive('foobar'));
        $this->assertFalse($instance->isActive('foobaz'));
        $this->assertFalse($instance->isActive('bazbar'));
    }

    public function testIsActiveCallback(): void
    {
        $features = [
            'foo' => function (array $data): bool {
                return $data['value'] === 123;
            },
            'bar' => function ($a, $b): bool {
                return ($a + $b) === 10;
            },
        ];

        $instance = new Toggle(StorageFactory::factory($features));

        // Call all these function twice to check that it is memoized correctly
        $this->assertTrue($instance->isActive('foo', [[
            'value' => 123,
        ]]));
        $this->assertTrue($instance->isActive('foo', [[
            'value' => 123,
        ]]));
        $this->assertFalse($instance->isActive('foo', [[
            'value' => 456,
        ]]));
        $this->assertFalse($instance->isActive('foo', [[
            'value' => 456,
        ]]));

        $this->assertTrue($instance->isActive('bar', [5, 5]));
        $this->assertTrue($instance->isActive('bar', [5, 5]));
        $this->assertFalse($instance->isActive('bar', [1, 2]));
        $this->assertFalse($instance->isActive('bar', [1, 2]));
    }

    public function testIsActiveExpression(): void
    {
        $features = [
            'foo' => new Expression('newValue > 10 and some["value"] < 10'),
        ];

        $instance = new Toggle(StorageFactory::factory($features));

        $this->assertTrue($instance->isActive('foo', [
            'newValue' => 123,
            'some' => [
                'value' => 5,
            ],
        ]));
        $this->assertFalse($instance->isActive('foo', [
            'newValue' => 123,
            'some' => [
                'value' => 500,
            ],
        ]));
    }

    public function testIsActiveStringClass(): void
    {
        $features = [
            'foo' => new class() {
                public function __toString(): string
                {
                    return '1';
                }
            },
            'bar' => new class() {
                public function __toString(): string
                {
                    return '0';
                }
            },
        ];

        $instance = new Toggle(StorageFactory::factory($features));

        $this->assertTrue($instance->isActive('foo'));
        $this->assertFalse($instance->isActive('bar'));
    }
}
