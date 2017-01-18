<?php

declare(strict_types=1);

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace SolidWorx\Tests\Toggler;

use SolidWorx\Toggler\Config;
use SolidWorx\Toggler\Toggle;
use Symfony\Component\ExpressionLanguage\Expression;

class ToggleTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $instance1 = Toggle::instance();
        $instance2 = Toggle::instance();

        $this->assertSame($instance1, $instance2);
    }

    public function testIsActive()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $config = Config::instance();

        $config->setConfig($features);

        $instance = Toggle::instance();

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

        $config = Config::instance();

        $config->setConfig($features);

        $instance = Toggle::instance();

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

        $config = Config::instance();

        $config->setConfig($features);

        $instance = Toggle::instance();

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

        $config = Config::instance();

        $config->setConfig($features);

        $instance = Toggle::instance();

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
        if (!class_exists('Symfony\Component\ExpressionLanguage\ExpressionLanguage')) {
            $this->markTestSkipped('The symfony/expression-language component is needed to test expressions');
        }

        $features = [
            'foo' => new Expression('newValue > 10 and some["value"] < 10'),
        ];

        $config = Config::instance();

        $config->setConfig($features);

        $instance = Toggle::instance();

        // Call all these function twice to check that it is memoized correctly
        $this->assertTrue($instance->isActive('foo', ['newValue' => 123, 'some' => ['value' => 5]]));
        $this->assertFalse($instance->isActive('foo', ['newValue' => 123, 'some' => ['value' => 500]]));
    }

    public function testExecute()
    {
        $instance = Toggle::instance();

        $this->assertSame(456, $instance->execute(function (): int {
            return 456;
        }));
    }

    public function testExecuteException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Feature is not available');

        $instance = Toggle::instance();

        $instance->execute(function () {
            throw new \Exception('Feature is not available');
        });
    }

    protected function tearDown()
    {
        Config::instance()->clear();
    }
}
