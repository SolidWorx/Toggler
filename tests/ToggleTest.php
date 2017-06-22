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

use PHPUnit\Framework\TestCase;
use SolidWorx\Toggler\Config;
use SolidWorx\Toggler\Toggle;
use Symfony\Component\ExpressionLanguage\Expression;

class ToggleTest extends TestCase
{
    public function testIsActive()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $instance = new Toggle(new Config($features));

        $this->assertTrue($instance->isActive('foo'));
        $this->assertTrue($instance->isActive('bar'));
        $this->assertFalse($instance->isActive('baz'));
        $this->assertFalse($instance->isActive('foobar'));
    }

    public function testIsActiveTruthy()
    {
        $features = [
            'foo' => true,
            'bar' => 1,
            'baz' => '1',
            'foobar' => 'on',
        ];

        $instance = new Toggle(new Config($features));

        $this->assertTrue($instance->isActive('foo'));
        $this->assertTrue($instance->isActive('bar'));
        $this->assertTrue($instance->isActive('baz'));
        $this->assertTrue($instance->isActive('foobar'));
    }

    public function testIsActiveFalsey()
    {
        $features = [
            'foo' => false,
            'bar' => 0,
            'baz' => '0',
            'foobar' => 'off',
            'foobaz' => [],
            'bazbar' => new \StdClass,
        ];

        $instance = new Toggle(new Config($features));

        $this->assertFalse($instance->isActive('foo'));
        $this->assertFalse($instance->isActive('bar'));
        $this->assertFalse($instance->isActive('baz'));
        $this->assertFalse($instance->isActive('foobar'));
        $this->assertFalse($instance->isActive('foobaz'));
        $this->assertFalse($instance->isActive('bazbar'));
    }

    public function testisActiveCallback()
    {
        $features = [
            'foo' => function (array $data): bool {
                return $data['value'] === 123;
            },
            'bar' => function ($a, $b): bool {
                return ($a + $b) === 10;
            },
        ];

        $instance = new Toggle(new Config($features));

        // Call all these function twice to check that it is memoized correctly
        $this->assertTrue($instance->isActive('foo', [['value' => 123]]));
        $this->assertTrue($instance->isActive('foo', [['value' => 123]]));
        $this->assertFalse($instance->isActive('foo', [['value' => 456]]));
        $this->assertFalse($instance->isActive('foo', [['value' => 456]]));

        $this->assertTrue($instance->isActive('bar', [5, 5]));
        $this->assertTrue($instance->isActive('bar', [5, 5]));
        $this->assertFalse($instance->isActive('bar', [1, 2]));
        $this->assertFalse($instance->isActive('bar', [1, 2]));
    }

    public function testisActiveExpression()
    {
        $features = [
            'foo' => new Expression('newValue > 10 and some["value"] < 10'),
        ];

        $instance = new Toggle(new Config($features));

        // Call all these function twice to check that it is memoized correctly
        $this->assertTrue($instance->isActive('foo', ['newValue' => 123, 'some' => ['value' => 5]]));
        $this->assertFalse($instance->isActive('foo', ['newValue' => 123, 'some' => ['value' => 500]]));
    }
}
