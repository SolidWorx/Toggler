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

class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testToggleConfig()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $this->assertSame($features, $this->readAttribute(new Config($features), 'config'));
    }

    public function testToggleConfigWithFile()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $this->assertSame($features, $this->readAttribute(new Config(__DIR__.'/stubs/config.php'), 'config'));
    }

    public function testToggleConfigWithYamlFile()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $this->assertSame($features, $this->readAttribute(new Config(__DIR__.'/stubs/config.yml'), 'config'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testToggle()
    {
        $callback = function (): string {
            return 'abcdef';
        };

        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        toggleConfig($features);

        $this->assertSame('abcdef', toggle('foo', $callback));
    }

    /**
     * @runInSeparateProcess
     */
    public function testToggleReturn()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        toggleConfig($features);

        $this->assertTrue(toggle('foo'));
        $this->assertTrue(toggle('bar'));
        $this->assertFalse(toggle('baz'));
        $this->assertFalse(toggle('foobar'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testToggleFail()
    {
        $callback = function (): string {
            return 'abcdef';
        };

        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        toggleConfig($features);

        $this->assertSame('abcdef', toggle('baz', function (): void { }, $callback));
        $this->assertNull(toggle('baz', function (): void { }));
    }
}