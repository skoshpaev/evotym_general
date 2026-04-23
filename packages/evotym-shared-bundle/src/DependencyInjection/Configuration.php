<?php

declare(strict_types=1);

namespace Evotym\SharedBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('evotym_shared');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('rabbitmq')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('product_exchange')->defaultValue('products.events')->end()
                        ->scalarNode('product_exchange_type')->defaultValue('topic')->end()
                        ->scalarNode('product_created_routing_key')->defaultValue('product.created')->end()
                        ->scalarNode('product_updated_routing_key')->defaultValue('product.updated')->end()
                        ->scalarNode('product_deleted_routing_key')->defaultValue('product.deleted')->end()
                        ->scalarNode('order_product_sync_queue')->defaultValue('orders.products.sync')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
