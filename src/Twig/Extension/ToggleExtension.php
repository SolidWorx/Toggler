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

namespace SolidWorx\Toggler\Twig\Extension;

use SolidWorx\Toggler\ToggleInterface;
use SolidWorx\Toggler\Twig\Parser\ToggleTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ToggleExtension extends AbstractExtension
{
    /**
     * @var ToggleInterface
     */
    private $toggle;

    public function __construct(ToggleInterface $toggle)
    {
        $this->toggle = $toggle;
    }

    public function getToggle(): ToggleInterface
    {
        return $this->toggle;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers(): array
    {
        return [new ToggleTokenParser()];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('toggle', [$this->toggle, 'isActive']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'toggler';
    }
}
