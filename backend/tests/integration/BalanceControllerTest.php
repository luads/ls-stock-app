<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\Helper\DatabasePrimer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BalanceControllerTest extends WebTestCase
{
    public function testCreateTransactionReturnsUsersFinalBalance(): void
    {
        $client = self::createClient();
        DatabasePrimer::prime($client->getKernel());

        $user = 'new-test-user';
        $balance = 100;

        $payload = json_encode([
            'balance' => $balance,
        ]);

        $client->request('POST', '/v1/balance/transaction', [], [], ['HTTP_X-User' => $user], $payload);
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertSame($balance, $content['balance']);
    }

    public function testCreateTransactionWithInvalidUserFails(): void
    {
        $client = self::createClient();

        $client->request('POST', '/v1/balance/transaction');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testCreateTransactionWithInvalidBalanceFails(): void
    {
        $client = self::createClient();

        $client->request('POST', '/v1/balance/transaction', [], [], ['HTTP_X-User' => 'test-user']);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }
}
