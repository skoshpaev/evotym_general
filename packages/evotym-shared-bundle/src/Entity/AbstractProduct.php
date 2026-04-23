<?php

declare(strict_types=1);

namespace Evotym\SharedBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class AbstractProduct
{
    public const NAME_MAX_LENGTH = 255;

    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true)]
    protected string $id;

    #[ORM\Column(length: self::NAME_MAX_LENGTH)]
    protected string $name;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    protected string $price;

    #[ORM\Column(type: Types::INTEGER)]
    protected int $quantity;

    protected function initializeProduct(string $id, string $name, float $price, int $quantity): void
    {
        $this->id = $id;
        $this->syncProductData($name, $price, $quantity);
    }

    public function syncProductData(string $name, float $price, int $quantity): void
    {
        $this->name = $name;
        $this->price = self::normalizePrice($price);
        $this->quantity = $quantity;
    }

    public function decreaseQuantity(int $quantity): void
    {
        if ($quantity > $this->quantity) {
            throw new \LogicException('Ordered quantity exceeds available stock.');
        }

        $this->quantity -= $quantity;
    }

    public function increaseQuantity(int $quantity): void
    {
        $this->quantity += $quantity;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return (float) $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    protected static function normalizePrice(float $price): string
    {
        return number_format($price, 2, '.', '');
    }
}
