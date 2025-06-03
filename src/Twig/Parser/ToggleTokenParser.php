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

namespace SolidWorx\Toggler\Twig\Parser;

use SolidWorx\Toggler\Twig\Node\ToggleNode;
use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use function method_exists;

class ToggleTokenParser extends AbstractTokenParser
{
    /**
     * @return ToggleNode<Node>
     * @throws SyntaxError
     */
    public function parse(Token $token): ToggleNode
    {
        $parser = method_exists($this->parser, 'parseExpression') ? $this->parser : $this->parser->getExpressionParser();

        $lineNo = $token->getLine();
        $feature = $parser->parseExpression();
        $stream = $this->parser->getStream();

        $variables = null;
        if ($stream->nextIf(Token::NAME_TYPE, 'with') !== null) {
            $variables = $parser->parseExpression();
        }

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideIfFork']);
        $else = null;
        $end = false;

        while (! $end) {
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

    public function getTag(): string
    {
        return 'toggle';
    }
}
