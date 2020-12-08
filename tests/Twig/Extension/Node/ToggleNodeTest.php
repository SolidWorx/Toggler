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

namespace SolidWorx\Toggler\Tests\Twig\Extension\Node;

use SolidWorx\Toggler\Twig\Node\ToggleNode;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\NameExpression;
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
            new PrintNode(new NameExpression('foo', 1), 1),
        ], [], 1);
        $else = null;
        $node = new ToggleNode(new TextNode('foo', 1), $t, $else, null, 1, null);

        self::assertEquals($t, $node->getNode('body'));
        self::assertEquals(new TextNode('foo', 1), $node->getNode('feature'));
        self::assertFalse($node->hasNode('else'));

        $else = new PrintNode(new NameExpression('bar', 1), 1);
        $node = new ToggleNode(new TextNode('bar', 1), $t, $else, null, 1, null);
        self::assertEquals($else, $node->getNode('else'));
    }

    /**
     * @return array<array{Node,string}>
     */
    public function getTests(): array
    {
        $tests = [];

        $tests[] = $this->getToggleTest();
        $tests[] = $this->getToggleWithElseTest();
        $tests[] = $this->getToggleWithContextTest();

        return $tests;
    }

    /**
     * @return array{Node,string}
     */
    private function getToggleTest(): array
    {
        $t = new Node([
            new PrintNode(new NameExpression('foo', 1), 1),
        ], [], 1, null);
        $else = null;
        $node = new ToggleNode(new Node([new ConstantExpression('foo', 1)]), $t, $else, null, 1);

        return [
            $node,
            <<<EOF
// line 1
if (\$this->env->getExtension('SolidWorx\Toggler\Twig\Extension\ToggleExtension')->getToggle()->isActive("foo")) {
    echo {$this->getVariableGetter('foo')};
}
EOF
            ,
        ];
    }

    /**
     * @return array{Node,string}
     */
    private function getToggleWithElseTest(): array
    {
        $t = new Node([
            new PrintNode(new NameExpression('foo', 1), 1),
        ], [], 1, null);
        $else = new PrintNode(new NameExpression('bar', 1), 1);
        $node = new ToggleNode(new Node([new ConstantExpression('foo', 1)]), $t, $else, null, 1);

        return [
            $node,
            <<<EOF
// line 1
if (\$this->env->getExtension('SolidWorx\Toggler\Twig\Extension\ToggleExtension')->getToggle()->isActive("foo")) {
    echo {$this->getVariableGetter('foo')};
} else {
    echo {$this->getVariableGetter('bar')};
}
EOF
            ,
        ];
    }

    /**
     * @return array{Node,string}
     */
    private function getToggleWithContextTest(): array
    {
        $t = new Node([
            new PrintNode(new NameExpression('foo', 1), 1),
        ], [], 1, null);

        $node = new ToggleNode(new Node([new ConstantExpression('foo', 1)]),
            $t,
            null,
            new ArrayExpression([new ConstantExpression('value1', 1), new ConstantExpression(12, 1)], 1),
            1
        );

        return [
            $node,
            <<<EOF
// line 1
if (\$this->env->getExtension('SolidWorx\Toggler\Twig\Extension\ToggleExtension')->getToggle()->isActive("foo", ["value1" => 12])) {
    echo {$this->getVariableGetter('foo')};
}
EOF
            ,
        ];
    }
}
