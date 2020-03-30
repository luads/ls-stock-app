<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Transaction;
use App\Model\ShareModel;
use App\Service\SharesService;
use App\Service\TransactionService;
use App\Share\ApiClientInterface;
use App\Share\ShareDetails;
use PHPUnit\Framework\TestCase;

class SharesServiceTest extends TestCase
{
    public function testPurchaseFetchesShareDetailsAndPushesTransaction(): void
    {
        $shareModel = $this->createMock(ShareModel::class);
        $apiClient = $this->createMock(ApiClientInterface::class);
        $transactionService = $this->createMock(TransactionService::class);

        $user = 'test-user';
        $shareName = 'TEST';
        $shareValue = 10;
        $details = new ShareDetails($shareName, $shareValue);

        $quantityToPurchase = 15;
        $expectedPrice = $shareValue * $quantityToPurchase * -1;

        $apiClient->method('get')->with($shareName)->willReturn($details);

        $shareModel->expects($this->once())
            ->method('createOrUpdate')
            ->with($user, $shareName, $quantityToPurchase);

        $transactionService->expects($this->once())
            ->method('push')
            ->with($user, $expectedPrice, Transaction::OPERATION_PURCHASE);

        $service = new SharesService($shareModel, $apiClient, $transactionService);
        $service->purchase($user, $shareName, $quantityToPurchase);
    }
}
