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

namespace SolidWorx\Toggler\Symfony\DependencyInjection;

use SolidWorx\Toggler\Config;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class TogglerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $definition = $container->getDefinition('toggler.toggle');
        $commandDefinition = $container->getDefinition('toggler.command.set_value');

        if (!empty($config['config']['storage'])) {
            $service = $config['config']['storage'];

            if ('@' !== $service[0]) {
                throw new InvalidConfigurationException('The service for the config toggler.config.storage should be in the format "@service.id"');
            }

            $service = substr($service, 1);

            $definition->replaceArgument(0, new Reference($service));
            $commandDefinition->replaceArgument(0, new Reference($service));

            return;
        }

        foreach ($config['config']['features'] as $key => &$value) {
            if (!is_string($value)) {
                continue;
            }

            switch (true) {

                case false !== strpos($value, '::') && '@' === $value[0]:
                    $parts = explode('::', $value);

                    $value = [new Reference(substr($parts[0], 1)), $parts[1]];
                    break;

                case '@=' === substr($value, 0, 2):

                    if (!class_exists(Expression::class)) {
                        throw new \InvalidArgumentException('The symfony/expression-language component is required in order to use expressions.');
                    }

                    $value = new Definition(Expression::class, [substr($value, 2)]);
                    break;
            }
        }

        $storageDefinition = new Definition(Config::class, [$config['config']['features']]);
        $storageDefinition->setPublic(false);
        $storageDefinition->setFactory(Config::class . '::factory');
        $storageDefinition->setLazy(true);

        $definition->replaceArgument(0, $storageDefinition);
        $commandDefinition->replaceArgument(0, $storageDefinition);
    }
}
