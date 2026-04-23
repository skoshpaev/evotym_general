<?php

declare(strict_types=1);

use Evotym\SharedBundle\RabbitMq\ProductRabbitMqTopologyProvider;
use Evotym\SharedBundle\RabbitMq\ProductRabbitMqTopologyProviderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(ProductRabbitMqTopologyProvider::class)
        ->arg('$exchangeName', '%evotym_shared.rabbitmq.product_exchange%')
        ->arg('$exchangeType', '%evotym_shared.rabbitmq.product_exchange_type%')
        ->arg('$productCreatedRoutingKey', '%evotym_shared.rabbitmq.product_created_routing_key%')
        ->arg('$productUpdatedRoutingKey', '%evotym_shared.rabbitmq.product_updated_routing_key%')
        ->arg('$productDeletedRoutingKey', '%evotym_shared.rabbitmq.product_deleted_routing_key%')
        ->arg('$orderProductSyncQueue', '%evotym_shared.rabbitmq.order_product_sync_queue%');

    $services->alias(ProductRabbitMqTopologyProviderInterface::class, ProductRabbitMqTopologyProvider::class);
};
