<?php

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace Toggler\Twig\Parser;

use Toggler\Twig\Node\ToggleNode;

class ToggleTokenParser extends \Twig_TokenParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $feature = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();
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
                        sprintf('Unexpected end of template. Twig was looking for the following tags "else", or "endtoggle" to close the "toggle" block started at line %d)', $lineno),
                        $stream->getCurrent()->getLine(),
                        $stream->getFilename()
                    );
            }
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new ToggleNode($feature, $body, $else, $lineno, $this->getTag());
    }

    /**
     * @param \Twig_Token $token
     *
     * @return bool
     */
    public function decideIfFork(\Twig_Token $token)
    {
        return $token->test(['else', 'endtoggle']);
    }

    /**
     * @param \Twig_Token $token
     *
     * @return bool
     */
    public function decideIfEnd(\Twig_Token $token)
    {
        return $token->test(['endtoggle']);
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'toggle';
    }
}