<?php

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
