<?php

declare(strict_types=1);

namespace Evotym\SharedBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class EvotymSharedExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('evotym_shared.rabbitmq.product_exchange', $config['rabbitmq']['product_exchange']);
        $container->setParameter('evotym_shared.rabbitmq.product_exchange_type', $config['rabbitmq']['product_exchange_type']);
        $container->setParameter('evotym_shared.rabbitmq.product_created_routing_key', $config['rabbitmq']['product_created_routing_key']);
        $container->setParameter('evotym_shared.rabbitmq.product_updated_routing_key', $config['rabbitmq']['product_updated_routing_key']);
        $container->setParameter('evotym_shared.rabbitmq.product_deleted_routing_key', $config['rabbitmq']['product_deleted_routing_key']);
        $container->setParameter('evotym_shared.rabbitmq.order_product_sync_queue', $config['rabbitmq']['order_product_sync_queue']);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.php');
    }
}
