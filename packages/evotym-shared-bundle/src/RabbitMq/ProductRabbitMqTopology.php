<?php

declare(strict_types=1);

namespace Evotym\SharedBundle\RabbitMq;

final class ProductRabbitMqTopology
{
    /**
     * @param list<RabbitMqQueueConfig> $queues
     */
    public function __construct(
        public readonly RabbitMqExchangeConfig $exchange,
        public readonly array $queues,
        public readonly string $productCreatedRoutingKey,
        public readonly string $productUpdatedRoutingKey,
        public readonly string $productDeletedRoutingKey,
    ) {
    }
}
