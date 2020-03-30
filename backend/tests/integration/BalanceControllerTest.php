<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Entity\Transaction;
use App\Tests\Helper\DatabasePrimer;
use App\Tests\Helper\Fixtures\PositiveTransaction;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BalanceControllerTest extends WebTestCase
{
    public function testGetBalanceReturnsExistingUsersBalance(): void
    {
        $fixture = new PositiveTransaction();

        $client = self::createClient();
        DatabasePrimer::prime($client->getKernel(), [$fixture]);

        $client->request('GET', '/v1/balance', [], [], ['HTTP_X-User' => PositiveTransaction::USER]);
        $content = json_decode($client->getResponse()->getContent(), true);

        /** @var Transaction $transaction */
        $transaction = $fixture->getReference(PositiveTransaction::REFERENCE);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertSame($transaction->getBalance(), $content['balance']);
    }

    public function testGetBalanceWithInvalidUserFails(): void
    {
        $client = self::createClient();

        $client->request('GET', '/v1/balance');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

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
