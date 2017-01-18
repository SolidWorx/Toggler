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
use Symfony\Component\Yaml\Yaml;

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

        toggleConfig($features);

        $this->assertSame($features, $this->readAttribute(Config::instance(), 'config'));
    }

    public function testToggleConfigWithFile()
    {
        toggleConfig(__DIR__.'/stubs/config.php');

        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $this->assertSame($features, $this->readAttribute(Config::instance(), 'config'));
    }

    public function testToggleConfigWithYamlFile()
    {
        if (!class_exists(Yaml::class)) {
            $this->markTestSkipped('The symfony/yaml component is needed to test yaml config files');
        }

        toggleConfig(__DIR__.'/stubs/config.yml');

        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $this->assertSame($features, $this->readAttribute(Config::instance(), 'config'));
    }

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

    protected function tearDown()
    {
        Config::instance()->clear();
    }
}