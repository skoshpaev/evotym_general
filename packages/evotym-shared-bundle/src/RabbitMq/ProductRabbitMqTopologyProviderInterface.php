<?php

declare(strict_types=1);

namespace Evotym\SharedBundle\RabbitMq;

interface ProductRabbitMqTopologyProviderInterface
{
    public function getTopology(): ProductRabbitMqTopology;
}
