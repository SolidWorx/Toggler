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

use Twig\Compiler;
use Twig\Node\Node;

class ToggleNode extends Node
{
    /**
     * @param Node<string>        $feature
     * @param Node<string>            $body
     * @param Node<string>|null       $else
     * @param Node<array<mixed>>|null $variables
     * @param int                     $lineNo
     * @param string|null             $tag
     */
    public function __construct(Node $feature, Node $body, ?Node $else, ?Node $variables, int $lineNo, string $tag = null)
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
