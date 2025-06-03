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

namespace SolidWorx\Toggler\Twig\Extension;

use SolidWorx\Toggler\ToggleInterface;
use SolidWorx\Toggler\Twig\Parser\ToggleTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ToggleExtension extends AbstractExtension
{
    private ToggleInterface $toggle;

    public function __construct(ToggleInterface $toggle)
    {
        $this->toggle = $toggle;
    }

    public function getToggle(): ToggleInterface
    {
        return $this->toggle;
    }

    public function getTokenParsers(): array
    {
        return [new ToggleTokenParser()];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toggle', [$this->toggle, 'isActive']),
        ];
    }

    public function getName(): string
    {
        return 'toggler';
    }
}
