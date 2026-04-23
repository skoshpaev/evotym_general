<?php

declare(strict_types=1);

namespace Evotym\SharedBundle\Dto;

use Evotym\SharedBundle\Entity\AbstractProduct;

final class ProductViewDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly float $price,
        public readonly int $quantity,
    ) {
    }

    public static function fromProduct(AbstractProduct $product): self
    {
        return new self(
            $product->getId(),
            $product->getName(),
            $product->getPrice(),
            $product->getQuantity(),
        );
    }

    /**
     * @return array{id: string, name: string, price: float, quantity: int}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
        ];
    }
}
