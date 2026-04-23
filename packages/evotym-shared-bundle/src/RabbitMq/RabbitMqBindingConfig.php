<?php

declare(strict_types=1);

namespace Evotym\SharedBundle\RabbitMq;

final class RabbitMqBindingConfig
{
    public function __construct(public readonly string $routingKey)
    {
    }
}
