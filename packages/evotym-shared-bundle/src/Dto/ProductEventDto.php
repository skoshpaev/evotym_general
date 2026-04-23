<?php

declare(strict_types=1);

namespace Evotym\SharedBundle\Dto;

final class ProductEventDto
{
    public function __construct(
        public readonly string $eventName,
        public readonly ProductViewDto $product,
        public readonly string $occurredAt,
    ) {
    }

    /**
     * @return array{
     *     eventName: string,
     *     product: array{id: string, name: string, price: float, quantity: int},
     *     occurredAt: string
     * }
     */
    public function toArray(): array
    {
        return [
            'eventName' => $this->eventName,
            'product' => $this->product->toArray(),
            'occurredAt' => $this->occurredAt,
        ];
    }
}
