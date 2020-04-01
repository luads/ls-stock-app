<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Transaction;
use App\Exception\InsufficientSharesException;
use App\Model\ShareModel;
use App\Share\ApiClientInterface;
use App\Share\ShareDetails;

class SharesService
{
    private ShareModel $shareModel;
    private ApiClientInterface $sharesClient;
    private TransactionService $transactionService;

    public function __construct(
        ShareModel $shareModel,
        ApiClientInterface $sharesClient,
        TransactionService $transactionService
    ) {
        $this->shareModel = $shareModel;
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
        $value = $share->getPrice() * $quantity * -1;

        $transaction = $this->transactionService->push($user, $value, Transaction::OPERATION_PURCHASE);
        $this->shareModel->createOrUpdate($user, $shareName, $quantity);

        return $transaction;
    }

    public function sell(string $user, string $shareName, int $quantity): Transaction
    {
        $userShare = $this->shareModel->getUserShares($user, $shareName);

        if (!$userShare || $userShare->getQuantity() < $quantity) {
            throw new InsufficientSharesException(sprintf('Not enough shares to sell %d', $quantity));
        }

        $share = $this->getDetails($shareName);

        $value = $share->getPrice() * $quantity;
        $quantity *= -1;

        $this->shareModel->createOrUpdate($user, $shareName, $quantity);

        return $this->transactionService->push($user, $value, Transaction::OPERATION_SALE);
    }
}
