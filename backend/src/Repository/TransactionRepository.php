<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function getUserBalance(string $user): float
    {
        $result = $this->createQueryBuilder('t')
            ->select('SUM(t.balance)')
            ->where('t.user = :user')
            ->groupBy('t.user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();

        return $result ? (float) current($result) : 0;
    }
}
