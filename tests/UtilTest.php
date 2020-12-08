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
use SolidWorx\Toggler\Util;

class UtilTest extends TestCase
{
    /**
     * @dataProvider isTruthyProvider
     *
     * @param mixed $value
     */
    public function testTruthy($value): void
    {
        self::assertTrue(Util::isTruthy($value));
    }

    /**
     * @dataProvider isNotTruthyProvider
     *
     * @param mixed $value
     */
    public function testNotTruthy($value): void
    {
        self::assertFalse(Util::isTruthy($value));
    }

    /**
     * @return iterable<mixed>
     */
    public function isTruthyProvider(): iterable
    {
        yield [true];
        yield [1];
        yield ['true'];
        yield ['1'];
        yield ['yes'];
        yield ['on'];
        yield ['y'];
        yield ['YES'];
        yield ['Y'];
    }

    /**
     * @return iterable<mixed>
     */
    public function isNotTruthyProvider(): iterable
    {
        yield [false];
        yield [0];
        yield ['false'];
        yield ['0'];
        yield ['no'];
        yield ['n'];
        yield ['off'];
        yield ['NO'];
        yield ['N'];
        yield [null];
        yield [new \stdClass()];
        yield [[]];
    }
}
