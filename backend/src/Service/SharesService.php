<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Transaction;
use App\Share\ApiClientInterface;
use App\Share\ShareDetails;

class SharesService
{
    private ApiClientInterface $sharesClient;
    private TransactionService $transactionService;

    public function __construct(ApiClientInterface $sharesClient, TransactionService $transactionService)
    {
        $this->sharesClient = $sharesClient;
        $this->transactionService = $transactionService;
    }

    public function getDetails(string $shareName): ShareDetails
    {
        return $this->sharesClient->get($shareName);
    }

    public function purchase(string $user, string $shareName, int $quantity): Transaction
    {
        $share = $this->getDetails($shareName);

        $value = $share->getPrice() * $quantity;

        return $this->transactionService->push($user, $value, Transaction::OPERATION_PURCHASE);
    }
}
