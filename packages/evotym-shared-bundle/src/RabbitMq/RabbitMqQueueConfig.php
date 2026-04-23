<?php

declare(strict_types=1);

namespace Evotym\SharedBundle\RabbitMq;

final class RabbitMqQueueConfig
{
    /**
     * @param list<RabbitMqBindingConfig> $bindings
     */
    public function __construct(
        public readonly string $name,
        public readonly array $bindings,
        public readonly bool $durable = true,
    ) {
    }
}
