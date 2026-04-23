<?php

declare(strict_types=1);

namespace Evotym\SharedBundle\RabbitMq;

final class ProductRabbitMqTopologyProvider implements ProductRabbitMqTopologyProviderInterface
{
    public function __construct(
        private readonly string $exchangeName,
        private readonly string $exchangeType,
        private readonly string $productCreatedRoutingKey,
        private readonly string $productUpdatedRoutingKey,
        private readonly string $productDeletedRoutingKey,
        private readonly string $orderProductSyncQueue,
    ) {
    }

    public function getTopology(): ProductRabbitMqTopology
    {
        return new ProductRabbitMqTopology(
            new RabbitMqExchangeConfig($this->exchangeName, $this->exchangeType),
            [
                new RabbitMqQueueConfig(
                    $this->orderProductSyncQueue,
                    [
                        new RabbitMqBindingConfig($this->productCreatedRoutingKey),
                        new RabbitMqBindingConfig($this->productUpdatedRoutingKey),
                        new RabbitMqBindingConfig($this->productDeletedRoutingKey),
                    ],
                ),
            ],
            $this->productCreatedRoutingKey,
            $this->productUpdatedRoutingKey,
            $this->productDeletedRoutingKey,
        );
    }
}
