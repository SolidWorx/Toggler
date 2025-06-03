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

namespace SolidWorx\Toggler\Tests\Twig\Extension\Node;

use SolidWorx\Toggler\Twig\Node\ToggleNode;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\Variable\ContextVariable;
use Twig\Node\Node;
use Twig\Node\PrintNode;
use Twig\Node\TextNode;
use Twig\Test\NodeTestCase;

class ToggleNodeTest extends NodeTestCase
{
    public function testConstructor(): void
    {
        $t = new Node([
            new ConstantExpression(true, 1),
            new PrintNode(new ContextVariable('foo', 1), 1),
        ], [], 1);
        $else = null;
        $node = new ToggleNode(new TextNode('foo', 1), $t, $else, null, 1, null);

        self::assertEquals($t, $node->getNode('body'));
        self::assertEquals(new TextNode('foo', 1), $node->getNode('feature'));
        self::assertFalse($node->hasNode('else'));

        $else = new PrintNode(new ContextVariable('bar', 1), 1);
        $node = new ToggleNode(new TextNode('bar', 1), $t, $else, null, 1, null);
        self::assertEquals($else, $node->getNode('else'));
    }

    /**
     * @return array<array{Node,string}>
     */
    public static function provideTests(): iterable
    {
        yield self::getToggleTest();
        yield self::getToggleWithElseTest();
        yield self::getToggleWithContextTest();
    }

    /**
     * @return array{Node,string}
     */
    private static function getToggleTest(): array
    {
        $t = new Node([
            new PrintNode(new ContextVariable('foo', 1), 1),
        ], [], 1, null);
        $else = null;
        $node = new ToggleNode(new Node([new ConstantExpression('foo', 1)]), $t, $else, null, 1);

        $var = self::createVariableGetter('foo');
        return [
            $node,
            <<<EOF
// line 1
if (\$this->env->getExtension('SolidWorx\Toggler\Twig\Extension\ToggleExtension')->getToggle()->isActive("foo")) {
    yield {$var};
}
EOF
            ,
        ];
    }

    /**
     * @return array{Node,string}
     */
    private static function getToggleWithElseTest(): array
    {
        $t = new Node([
            new PrintNode(new ContextVariable('foo', 1), 1),
        ], [], 1, null);
        $else = new PrintNode(new ContextVariable('bar', 1), 1);
        $node = new ToggleNode(new Node([new ConstantExpression('foo', 1)]), $t, $else, null, 1);

        $varFoo = self::createVariableGetter('foo');
        $varBar = self::createVariableGetter('bar');
        return [
            $node,
            <<<EOF
// line 1
if (\$this->env->getExtension('SolidWorx\Toggler\Twig\Extension\ToggleExtension')->getToggle()->isActive("foo")) {
    yield {$varFoo};
} else {
    yield {$varBar};
}
EOF
            ,
        ];
    }

    /**
     * @return array{Node,string}
     */
    private static function getToggleWithContextTest(): array
    {
        $t = new Node([
            new PrintNode(new ContextVariable('foo', 1), 1),
        ], [], 1, null);

        $node = new ToggleNode(
            new Node([new ConstantExpression('foo', 1)]),
            $t,
            null,
            new ArrayExpression([new ConstantExpression('value1', 1), new ConstantExpression(12, 1)], 1),
            1
        );

        $var = self::createVariableGetter('foo');
        return [
            $node,
            <<<EOF
// line 1
if (\$this->env->getExtension('SolidWorx\Toggler\Twig\Extension\ToggleExtension')->getToggle()->isActive("foo", ["value1" => 12])) {
    yield {$var};
}
EOF
            ,
        ];
    }
}
