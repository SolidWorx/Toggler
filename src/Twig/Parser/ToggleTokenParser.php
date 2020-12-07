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

namespace SolidWorx\Toggler\Twig\Parser;

use SolidWorx\Toggler\Twig\Node\ToggleNode;
use Twig\Error\SyntaxError;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class ToggleTokenParser extends AbstractTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(Token $token): ToggleNode
    {
        $lineNo = $token->getLine();
        $feature = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();

        $variables = null;
        if ($stream->nextIf(Token::NAME_TYPE, 'with')) {
            $variables = $this->parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideIfFork']);
        $else = null;
        $end = false;

        while (!$end) {
            switch ($stream->next()->getValue()) {
                case 'else':
                    $stream->expect(Token::BLOCK_END_TYPE);
                    $else = $this->parser->subparse([$this, 'decideIfEnd']);
                    break;

                case 'endtoggle':
                    $end = true;
                    break;

                default:
                    throw new SyntaxError(sprintf('Unexpected end of template. Twig was looking for the following tags "else", or "endtoggle" to close the "toggle" block started at line %d)', $lineNo), $stream->getCurrent()->getLine(), $stream->getSourceContext());
            }
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return new ToggleNode($feature, $body, $else, $variables, $lineNo, $this->getTag());
    }

    public function decideIfFork(Token $token): bool
    {
        return $token->test(['else', 'endtoggle']);
    }

    public function decideIfEnd(Token $token): bool
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
