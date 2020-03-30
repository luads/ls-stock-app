<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Transaction;
use Doctrine\Persistence\ManagerRegistry;

class TransactionModel
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function create(Transaction $transaction): Transaction
    {
        $this->registry->getManager()->persist($transaction);
        $this->registry->getManager()->flush();

        return $transaction;
    }
}
