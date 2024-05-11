<?php

namespace App\OrderManager;

final class OrderItemManager
{
    private float $taxRate = 0.2;

    public function __construct(
        private int $id,
        private string $image,
        private float $prix,
        private int $quantity,
        private bool $withRepair,
        private string $marque,
        private string $slug,
    ) {
    }

    public function getTotalPrice(): float
    {
        return $this->TotalPriceItem() * (1 + $this->taxRate);
    }

    public function getTaxAmount(): float
    {
        return $this->TotalPriceItem() * $this->taxRate;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function increaseQuantity(int $amount)
    {
        $this->quantity += $amount;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): void
    {
        $this->prix = $prix;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function isWithRepair(): bool
    {
        return $this->withRepair;
    }

    public function setWithRepair(bool $withRepair): void
    {
        $this->withRepair = $withRepair;
    }

    public function getMarque(): string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): void
    {
        $this->marque = $marque;
    }

    public function TotalPriceItem(): float
    {
        return $this->getQuantity() * $this->getPrix();
    }
}
