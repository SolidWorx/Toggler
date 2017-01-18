<?php

declare(strict_types=1);

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace SolidWorx\Toggler\Twig\Extension;

use SolidWorx\Toggler\Twig\Parser\ToggleTokenParser;

class ToggleExtension extends \Twig_Extension
{
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
            new \Twig_SimpleFunction('toggle', 'toggle'),
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