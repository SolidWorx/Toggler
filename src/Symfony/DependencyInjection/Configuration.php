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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('toggler');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('config')
                    ->isRequired()
                    ->children()
                        ->scalarNode('storage')
                            ->info('Set the storage handler service')
                            ->example('@redis.storage')
                            ->cannotBeEmpty()
                            ->defaultNull()
                            ->treatTrueLike(null)
                            ->treatFalseLike(null)
                        ->end()
                        ->arrayNode('features')
                            ->useAttributeAsKey('name')
                            ->info('An array containing available features. The feature name is the key, and the status of the feature is the value')
                            ->example([
                                'foo' => 'true',
                                'bar' => 'false',
                            ])
                            ->prototype('scalar')
                                ->beforeNormalization()
                                    ->ifArray()
                                        ->then(function (array $value): string {
                                            if (count($value) !== 2) {
                                                throw new InvalidConfigurationException('Callbacks should contain exactly two keys');
                                            }

                                            return implode('::', $value);
                                        })
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function (array $config): bool {
                            return $config['storage'] !== null && $config['features'] !== [];
                        })
                        ->thenInvalid('You should only specify one of "storage" or "features" values, not both.')
                    ->end()
                    ->validate()
                        ->ifTrue(function (array $config): bool {
                            return $config['storage'] === null && $config['features'] === [];
                        })
                        ->thenInvalid('At least one of "storage" or "features" must be set.')
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
