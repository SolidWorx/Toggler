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

use SolidWorx\Toggler\Symfony\TogglerBundle;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

require __DIR__ . '/../vendor/autoload.php';

$kernel = new class('dev', true) extends Kernel {
    public function registerBundles(): iterable
    {
        yield new TogglerBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/stubs/bundle_config.yml', 'yaml');
    }
};

return new Application($kernel);
