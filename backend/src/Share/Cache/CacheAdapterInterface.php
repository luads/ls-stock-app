<?php

declare(strict_types=1);

namespace App\Share\Cache;

interface CacheAdapterInterface
{
    public function get(string $shareName): ?float;
    public function set(string $shareName, float $value): void;
}
