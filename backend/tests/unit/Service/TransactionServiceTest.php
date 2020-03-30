<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Transaction;
use App\Exception\InsufficientFundsException;
use App\Model\TransactionModel;
use App\Repository\TransactionRepository;
use App\Service\TransactionService;
use PHPUnit\Framework\TestCase;

class TransactionServiceTest extends TestCase
{
    public function testTransactionGetsCreatedIfBalanceIsValid(): void
    {
        $model = $this->createMock(TransactionModel::class);
        $repository = $this->createMock(TransactionRepository::class);

        $model->expects($this->once())->method('create')->with($this->callback(function (Transaction $transaction) {
            $this->assertSame('test-user', $transaction->getUser());
            $this->assertSame(15.30, $transaction->getBalance());

            return true;
        }));

        $service = new TransactionService($model, $repository);
        $service->push('test-user', 15.30);
    }

    public function testPushFailsIfUserHasNoEnoughFunds(): void
    {
        $model = $this->createMock(TransactionModel::class);
        $repository = $this->createMock(TransactionRepository::class);

        $repository->method('getUserBalance')->willReturn(10.0);

        $this->expectException(InsufficientFundsException::class);

        $service = new TransactionService($model, $repository);
        $service->push('test-user', -1000000);
    }
}
