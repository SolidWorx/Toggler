<?php

declare(strict_types=1);

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace SolidWorx\Toggler\Twig\Parser;

use SolidWorx\Toggler\Twig\Node\ToggleNode;

class ToggleTokenParser extends \Twig_TokenParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(\Twig_Token $token): ToggleNode
    {
        $lineNo = $token->getLine();
        $feature = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();

        $variables = null;
        if ($stream->nextIf(\Twig_Token::NAME_TYPE, 'with')) {
            $variables = $this->parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideIfFork']);
        $else = null;
        $end = false;

        while (!$end) {
            switch ($stream->next()->getValue()) {
                case 'else':
                    $stream->expect(\Twig_Token::BLOCK_END_TYPE);
                    $else = $this->parser->subparse([$this, 'decideIfEnd']);
                    break;

                case 'endtoggle':
                    $end = true;
                    break;

                default:
                    throw new \Twig_Error_Syntax(
                        sprintf('Unexpected end of template. Twig was looking for the following tags "else", or "endtoggle" to close the "toggle" block started at line %d)', $lineNo),
                        $stream->getCurrent()->getLine(),
                        $stream->getSourceContext()
                    );
            }
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new ToggleNode($feature, $body, $else, $variables, $lineNo, $this->getTag());
    }

    /**
     * @param \Twig_Token $token
     *
     * @return bool
     */
    public function decideIfFork(\Twig_Token $token): bool
    {
        return $token->test(['else', 'endtoggle']);
    }

    /**
     * @param \Twig_Token $token
     *
     * @return bool
     */
    public function decideIfEnd(\Twig_Token $token): bool
    {
        return $token->test(['endtoggle']);
    }

    /**
     * {@inheritdoc}
     */
    public function getTag(): string
    {
        return 'toggle';
    }
}