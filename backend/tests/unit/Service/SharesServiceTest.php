<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Transaction;
use App\Service\SharesService;
use App\Service\TransactionService;
use App\Share\ApiClientInterface;
use App\Share\ShareDetails;
use PHPUnit\Framework\TestCase;

class SharesServiceTest extends TestCase
{
    public function testPurchaseFetchesShareDetailsAndPushesTransaction(): void
    {
        $apiClient = $this->createMock(ApiClientInterface::class);
        $transactionService = $this->createMock(TransactionService::class);

        $shareName = 'TEST';
        $shareValue = 10;
        $details = new ShareDetails($shareName, $shareValue);

        $quantityToPurchase = 15;
        $expectedPrice = $shareValue * $quantityToPurchase;

        $apiClient->method('get')->with($shareName)->willReturn($details);

        $transactionService->expects($this->once())
            ->method('push')
            ->with($this->anything(), $expectedPrice, Transaction::OPERATION_PURCHASE);

        $service = new SharesService($apiClient, $transactionService);
        $service->purchase('test-user', $shareName, $quantityToPurchase);
    }
}
