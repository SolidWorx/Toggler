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

namespace SolidWorx\Toggler\Symfony;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use SolidWorx\Toggler\Config;

class TogglerBundle extends Bundle
{
    public function boot(): void
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