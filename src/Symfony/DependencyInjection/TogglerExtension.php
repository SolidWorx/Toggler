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

namespace SolidWorx\Toggler\Symfony\DependencyInjection;

use InvalidArgumentException;
use SolidWorx\Toggler\Storage\StorageFactory;
use SolidWorx\Toggler\Symfony\Command\ToggleSetCommand;
use SolidWorx\Toggler\Toggle;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use function class_exists;
use function explode;
use function is_string;
use function str_contains;
use function str_starts_with;
use function substr;

class TogglerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $definition = $container->getDefinition(Toggle::class);
        $commandDefinition = $container->getDefinition(ToggleSetCommand::class);

        if ($config['config']['storage'] !== null) {
            $service = $config['config']['storage'];

            if ($service[0] !== '@') {
                throw new InvalidConfigurationException('The service for the config toggler.config.storage should be in the format "@service.id"');
            }

            $service = substr($service, 1);

            $definition->replaceArgument(0, new Reference($service));
            $commandDefinition->replaceArgument(0, new Reference($service));

            return;
        }

        foreach ($config['config']['features'] as &$value) {
            if (! is_string($value)) {
                continue;
            }

            switch (true) {
                case str_contains($value, '::') && $value[0] === '@':
                    $parts = explode('::', $value);

                    $value = [new Reference(substr($parts[0], 1)), $parts[1]];
                    break;

                case str_starts_with($value, '@='):
                    if (! class_exists(Expression::class)) {
                        throw new InvalidArgumentException('The symfony/expression-language component is required in order to use expressions.');
                    }

                    $value = new Expression(substr($value, 2));
                    break;
            }
        }

        $storageDefinition = new Definition(StorageFactory::class, [$config['config']['features']]);
        $storageDefinition->setPublic(false);
        $storageDefinition->setFactory(StorageFactory::class . '::factory');

        $container->addDefinitions([
            'toggler.storage' => $storageDefinition,
        ]);

        $definition->replaceArgument(0, $storageDefinition);
        $commandDefinition->replaceArgument(0, $storageDefinition);
    }
}
