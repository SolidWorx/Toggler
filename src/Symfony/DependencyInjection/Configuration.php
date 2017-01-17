<?php

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace SolidWorx\Toggler\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('toggler');

        $rootNode->children()
            ->arrayNode('config')
                ->beforeNormalization()
                    ->ifString()
                        ->then(function($value) { return array('service' => $value); })
                    ->end()
                ->prototype('scalar')
            ->end()
        ->end();

        return $treeBuilder;
    }
}