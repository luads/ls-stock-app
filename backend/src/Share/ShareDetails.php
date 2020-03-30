<?php

declare(strict_types=1);

namespace App\Share;

use JsonSerializable;

class ShareDetails implements JsonSerializable
{
    private string $name;
    private float $price;

    public function __construct(string $name, float $price)
    {
        $this->name = $name;
        $this->price = $price;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'price' => $this->getPrice(),
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}
