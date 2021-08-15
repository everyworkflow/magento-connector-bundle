<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\MagentoConnectorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('magento_connector');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
            ->arrayNode('connection')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('base_url')->defaultValue('')->end()
            ->scalarNode('api_end_point')->defaultValue('')->end()
            ->scalarNode('consumer_key')->defaultValue('')->end()
            ->scalarNode('consumer_secret')->defaultValue('')->end()
            ->scalarNode('access_token')->defaultValue('')->end()
            ->scalarNode('access_token_secret')->defaultValue('')->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
