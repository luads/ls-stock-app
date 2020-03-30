<?php

declare(strict_types=1);

namespace App\Share\Cache;

use Predis\Client;

class RedisCacheAdapter implements CacheAdapterInterface
{
    private Client $client;
    private int $ttl;

    public function __construct(Client $client, int $ttl)
    {
        $this->client = $client;
        $this->ttl = $ttl;
    }

    public function get(string $shareName): ?float
    {
        $key = $this->buildKey($shareName);
        $price = $this->client->get($key);

        if ($price) {
            return (float) $price;
        }

        return null;
    }

    public function set(string $shareName, float $value): void
    {
        $key = $this->buildKey($shareName);
        $this->client->set($key, $value, 'EX', $this->ttl);
    }

    private function buildKey(string $shareName): string
    {
        return sprintf('share/%s', $shareName);
    }
}
