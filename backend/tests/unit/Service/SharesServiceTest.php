<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Share;
use App\Entity\Transaction;
use App\Exception\InsufficientSharesException;
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

    public function testSellFetchesShareDetailsAndPushesTransaction(): void
    {
        $shareModel = $this->createMock(ShareModel::class);
        $apiClient = $this->createMock(ApiClientInterface::class);
        $transactionService = $this->createMock(TransactionService::class);

        $user = 'test-user';
        $shareName = 'TEST';
        $shareValue = 10;

        $details = new ShareDetails($shareName, $shareValue);
        $userShare = (new Share())->setQuantity(50);

        $quantityToSell = 15;
        $expectedPrice = $shareValue * $quantityToSell;

        $shareModel->method('getUserShares')->willReturn($userShare);
        $apiClient->method('get')->with($shareName)->willReturn($details);

        $shareModel->expects($this->once())
            ->method('createOrUpdate')
            ->with($user, $shareName, ($quantityToSell * -1));

        $transactionService->expects($this->once())
            ->method('push')
            ->with($user, $expectedPrice, Transaction::OPERATION_SALE);

        $service = new SharesService($shareModel, $apiClient, $transactionService);
        $service->sell($user, $shareName, $quantityToSell);
    }

    public function testSellFailsIfUserDoesNotHaveEnoughShares(): void
    {
        $shareModel = $this->createMock(ShareModel::class);
        $apiClient = $this->createMock(ApiClientInterface::class);
        $transactionService = $this->createMock(TransactionService::class);

        $user = 'test-user';
        $shareName = 'TEST';
        $details = new ShareDetails($shareName, 1);

        $quantityToSell = 10;

        $apiClient->method('get')->with($shareName)->willReturn($details);
        $shareModel->method('getUserShares')->willReturn(null);

        $this->expectException(InsufficientSharesException::class);

        $service = new SharesService($shareModel, $apiClient, $transactionService);
        $service->sell($user, $shareName, $quantityToSell);
    }
}
