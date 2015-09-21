<?php

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace Toggler\Symfony;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Toggler\Config;

class TogglerBundle extends Bundle
{
    public function boot()
    {
        $config = null;

        if ($this->container->hasParameter('toggler.config')) {
            $config = $this->container->getParameter('toggler.config');
        } else if($this->container->has('toggler.config')) {
            $config = $this->container->get('toggler.config');
        }

        Config::instance()->setConfig($config);
    }
}