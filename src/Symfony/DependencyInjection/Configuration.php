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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('toggler');
        $rootNode = $treeBuilder->getRootNode();

        // @phpstan-ignore-next-line
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
                            ->example(['foo' => 'true', 'bar' => 'false'])
                            ->prototype('scalar')
                                ->beforeNormalization()
                                    ->ifArray()
                                        ->then(function (array $value): string {
                                            if (2 !== count($value)) {
                                                throw new InvalidConfigurationException('Callbacks should contain exactly two keys');
                                            }

                                            return implode('::', $value);
                                        })
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function ($config): bool {
                            return null !== $config['storage'] && [] !== $config['features'];
                        })
                        ->thenInvalid('You should only specify one of "storage" or "features" values, not both.')
                    ->end()
                    ->validate()
                        ->ifTrue(function ($config): bool {
                            return null == $config['storage'] && null == $config['features'];
                        })
                        ->thenInvalid('At least one of "storage" or "features" must be set.')
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
