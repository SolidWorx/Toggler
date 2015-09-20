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
        $config = $this->container->getParameter('toggler.config');

        Config::instance()->setConfig($config);
    }
}