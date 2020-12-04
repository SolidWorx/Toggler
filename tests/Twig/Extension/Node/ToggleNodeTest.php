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

namespace SolidWorx\Tests\Toggler\Twig\Extension\Node;

use SolidWorx\Toggler\Twig\Node\ToggleNode;

if (class_exists('Twig_Test_NodeTestCase')) {
    class ToggleNodeTest extends \Twig_Test_NodeTestCase
    {
        public function testConstructor()
        {
            $t = new \Twig_Node([
                new \Twig_Node_Expression_Constant(true, 1),
                new \Twig_Node_Print(new \Twig_Node_Expression_Name('foo', 1), 1),
            ], [], 1);
            $else = null;
            $node = new ToggleNode(new \Twig_Node_Text('foo', 1), $t, $else, null, 1, null);

            $this->assertEquals($t, $node->getNode('body'));
            $this->assertEquals(new \Twig_Node_Text('foo', 1), $node->getNode('feature'));
            $this->assertFalse($node->hasNode('else'));

            $else = new \Twig_Node_Print(new \Twig_Node_Expression_Name('bar', 1), 1);
            $node = new ToggleNode(new \Twig_Node_Text('bar', 1), $t, $else, null, 1, null);
            $this->assertEquals($else, $node->getNode('else'));
        }

        public function getTests(): array
        {
            $tests = [];

            $tests[] = $this->getToggleTest();
            $tests[] = $this->getToggleWithElseTest();
            $tests[] = $this->getToggleWithContextTest();

            return $tests;
        }

        private function getToggleTest(): array
        {
            $t = new \Twig_Node([
                new \Twig_Node_Print(new \Twig_Node_Expression_Name('foo', 1), 1),
            ], [], null, 1);
            $else = null;
            $node = new ToggleNode(new \Twig_Node([new \Twig_Node_Expression_Constant('foo', 1)]), $t, $else, null, 1);

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

        private function getToggleWithElseTest(): array
        {
            $t = new \Twig_Node([
                new \Twig_Node_Print(new \Twig_Node_Expression_Name('foo', 1), 1),
            ], [], null, 1);
            $else = new \Twig_Node_Print(new \Twig_Node_Expression_Name('bar', 1), 1);
            $node = new ToggleNode(new \Twig_Node([new \Twig_Node_Expression_Constant('foo', 1)]), $t, $else, null, 1);

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

        private function getToggleWithContextTest(): array
        {
            $t = new \Twig_Node([
                new \Twig_Node_Print(new \Twig_Node_Expression_Name('foo', 1), 1),
            ], [], null, 1);

            $node = new ToggleNode(new \Twig_Node([new \Twig_Node_Expression_Constant('foo', 1)]),
                $t,
                null,
                new \Twig_Node_Expression_Array([new \Twig_Node_Expression_Constant('value1', 1), new \Twig_Node_Expression_Constant(12, 1)], 1),
                1
            );

            return [
                $node,
                <<<EOF
// line 1
if (\$this->env->getExtension('SolidWorx\Toggler\Twig\Extension\ToggleExtension')->getToggle()->isActive("foo", array("value1" => 12))) {
    echo {$this->getVariableGetter('foo')};
}
EOF
    ,
            ];
        }
    }
}
