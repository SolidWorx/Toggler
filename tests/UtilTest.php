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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SolidWorx\Toggler\Util;
use stdClass;

class UtilTest extends TestCase
{
    /**
     * @param mixed $value
     */
    #[DataProvider('isTruthyProvider')]
    public function testTruthy(bool|int|string $value): void
    {
        $this->assertTrue(Util::isTruthy($value));
    }

    /**
     * @param mixed $value
     */
    #[DataProvider('isNotTruthyProvider')]
    public function testNotTruthy(bool|int|string|stdClass|array|null $value): void
    {
        $this->assertFalse(Util::isTruthy($value));
    }

    /**
     * @return iterable<mixed>
     */
    public static function isTruthyProvider(): iterable
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
    public static function isNotTruthyProvider(): iterable
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
        yield [new stdClass()];
        yield [[]];
    }
}
