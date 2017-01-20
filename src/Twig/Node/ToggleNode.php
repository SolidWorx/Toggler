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

namespace SolidWorx\Toggler\Twig\Node;

class ToggleNode extends \Twig_Node
{
    /**
     * @param \Twig_Node $feature
     * @param \Twig_Node $body
     * @param \Twig_Node $else
     * @param \Twig_Node $variables
     * @param int        $lineNo
     * @param string     $tag
     */
    public function __construct(\Twig_Node $feature, \Twig_Node $body, ?\Twig_Node $else, ?\Twig_Node $variables, int $lineNo, string $tag = null)
    {
        $nodes = [
            'feature' => $feature,
            'body' => $body,
        ];

        if (null !== $else) {
            $nodes['else'] = $else;
        }

        if (null !== $variables) {
            $nodes['variables'] = $variables;
        }

        parent::__construct($nodes, [], $lineNo, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler): void
    {
        $compiler->addDebugInfo($this);

        $compiler
            ->write('if (')
            ->raw('toggle(')
            ->subcompile($this->getNode('feature'));

        if ($this->hasNode('variables')) {
            $compiler->raw(', ')
                ->subcompile($this->getNode('variables'));
        }

        $compiler
            ->raw(')')
            ->raw(") {\n")
            ->indent()
            ->subcompile($this->getNode('body'));

        if ($this->hasNode('else')) {
            $compiler
                ->outdent()
                ->write("} else {\n")
                ->indent()
                ->subcompile($this->getNode('else'));
        }

        $compiler
            ->outdent()
            ->write("}\n");
    }
}