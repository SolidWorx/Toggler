<?php

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace Tests\Toggler;

use Toggler\Config;
use Toggler\Toggle;

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
            'foobar' => false
        ];

        $config = Config::instance();

        $config->setConfig($features);

        $instance = Toggle::instance();

        $this->assertTrue($instance->isActive('foo'));
        $this->assertTrue($instance->isActive('bar'));
        $this->assertFalse($instance->isActive('baz'));
        $this->assertFalse($instance->isActive('foobar'));
    }

    public function testExecute()
    {
        $instance = Toggle::instance();

        $this->assertSame(456, $instance->execute(function () {
            return 456;
        }));
    }

    public function testExecuteException()
    {
        $this->setExpectedException('Exception', 'Feature is not available');

        $instance = Toggle::instance();

        $instance->execute(function () {
            throw new \Exception('Feature is not available');
        });
    }
}
