<?php

declare(strict_types=1);

namespace App\Share\AlphaVantage;

use App\Share\ApiClientInterface;
use App\Share\Cache\CacheAdapterInterface;
use App\Share\Exception\RateLimitedException;
use App\Share\Exception\ServiceUnavailableException;
use App\Share\Exception\ShareNotFoundException;
use App\Share\ShareDetails;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiClient implements ApiClientInterface
{
    private ClientInterface $httpClient;
    private string $apiKey;
    private CacheAdapterInterface $cacheAdapter;

    public function __construct(ClientInterface $httpClient, string $apiKey, CacheAdapterInterface $cacheAdapter)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->cacheAdapter = $cacheAdapter;
    }

    public function get(string $name): ShareDetails
    {
        if ($cachedPrice = $this->cacheAdapter->get($name)) {
            return new ShareDetails($name, $cachedPrice);
        }

        $endpoint = sprintf('query?function=GLOBAL_QUOTE&symbol=%s&apikey=%s', $name, $this->apiKey);
        $response = $this->httpClient->request('GET', $endpoint);

        $contents = json_decode($response->getBody()->getContents(), true);
        $price = (float) ($contents['Global Quote']['05. price'] ?? null);

        $this->validateShareResponse($response, $contents);

        $this->cacheAdapter->set($name, $price);

        return new ShareDetails($name, $price);
    }

    private function validateShareResponse(ResponseInterface $response, array $contents): void
    {
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new ServiceUnavailableException('Alpha Vantage API is unavailable');
        }

        $rateLimitedMessage = $contents['Note'] ?? null;
        $hasErrorMessage = $contents['Error Message'] ?? null;

        if ($hasErrorMessage) {
            throw new ShareNotFoundException('Share not found');
        }

        if ($rateLimitedMessage) {
            throw new RateLimitedException('Alpha Vantage API is rate limited, please try again later');
        }
    }
}
