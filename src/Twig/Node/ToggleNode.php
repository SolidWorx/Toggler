<?php

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace Toggler\Twig\Node;

class ToggleNode extends \Twig_Node
{
    /**
     * @param $feature
     * @param \Twig_NodeInterface      $body
     * @param \Twig_NodeInterface|null $else
     * @param null|string              $lineno
     * @param null                     $tag
     */
    public function __construct(
        $feature,
        \Twig_NodeInterface $body,
        \Twig_NodeInterface $else = null,
        $lineno,
        $tag = null
    ) {
        parent::__construct(
            [
                'feature' => $feature,
                'body' => $body,
                'else' => $else,
            ],
            [],
            $lineno,
            $tag
        );
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $compiler
            ->write('if (')
            ->raw('toggle(')
            ->subcompile($this->getNode('feature'))
            ->raw(')')
            ->raw(") {\n")
            ->indent()
            ->subcompile($this->getNode('body'));

        if ($this->hasNode('else') && null !== $this->getNode('else')) {
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