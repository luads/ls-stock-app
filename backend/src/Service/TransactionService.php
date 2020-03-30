<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Transaction;
use App\Exception\InsufficientFundsException;
use App\Model\TransactionModel;
use App\Repository\TransactionRepository;

class TransactionService
{
    private TransactionModel $transactionModel;
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionModel $transactionModel, TransactionRepository $transactionRepository)
    {
        $this->transactionModel = $transactionModel;
        $this->transactionRepository = $transactionRepository;
    }

    public function getCurrentBalance(string $user): float
    {
        return $this->transactionRepository->getUserBalance($user);
    }

    public function push(string $user, float $value, string $operation = null): Transaction
    {
        $currentBalance = $this->getCurrentBalance($user);
        $newBalance = $currentBalance + $value;

        if ($newBalance < 0) {
            throw new InsufficientFundsException('Not enough funds available to perform transaction');
        }

        if (!$operation) {
            $operation = ($value > 0) ? Transaction::OPERATION_DEPOSIT : Transaction::OPERATION_WITHDRAW;
        }

        $transaction = (new Transaction())
            ->setUser($user)
            ->setOperation($operation)
            ->setBalance($value);

        return $this->transactionModel->create($transaction);
    }
}
