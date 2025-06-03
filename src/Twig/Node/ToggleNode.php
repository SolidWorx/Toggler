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

namespace SolidWorx\Toggler\Twig\Node;

use Twig\Compiler;
use Twig\Node\Node;

class ToggleNode extends Node
{
    public function __construct(Node $feature, Node $body, ?Node $else, ?Node $variables, int $lineNo, ?string $tag = null)
    {
        $nodes = [
            'feature' => $feature,
            'body' => $body,
        ];

        if ($else instanceof Node) {
            $nodes['else'] = $else;
        }

        if ($variables instanceof Node) {
            $nodes['variables'] = $variables;
        }

        parent::__construct($nodes, [], $lineNo, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Compiler $compiler A Twig_Compiler instance
     */
    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this);

        $compiler
            ->write('if (')
            ->raw("\$this->env->getExtension('SolidWorx\\Toggler\\Twig\\Extension\\ToggleExtension')->getToggle()->isActive(")
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
