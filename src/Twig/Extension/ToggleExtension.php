<?php

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace Toggler\Twig\Extension;

use Toggler\Twig\Parser\ToggleTokenParser;

class ToggleExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [new ToggleTokenParser()];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('toggle', 'toggle'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'toggler';
    }
}