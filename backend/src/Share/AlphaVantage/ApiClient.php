<?php

declare(strict_types=1);

namespace App\Share\AlphaVantage;

use App\Share\ApiClientInterface;
use App\Share\Exception\RateLimitedException;
use App\Share\Exception\ServiceUnavailableException;
use App\Share\ShareDetails;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiClient implements ApiClientInterface
{
    private ClientInterface $httpClient;
    private string $apiKey;

    public function __construct(ClientInterface $httpClient, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    public function get(string $name): ShareDetails
    {
        $endpoint = sprintf('query?function=GLOBAL_QUOTE&symbol=%s&apikey=%s', $name, $this->apiKey);
        $response = $this->httpClient->request('GET', $endpoint);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new ServiceUnavailableException('Alpha Vantage API is unavailable');
        }

        $contents = json_decode($response->getBody()->getContents(), true);
        $price = $contents['Global Quote']['05. price'] ?? null;

        if (!$price) {
            throw new RateLimitedException('Alpha Vantage API is rate limited, please try again later');
        }

        return new ShareDetails($name, (float) $price);
    }
}
