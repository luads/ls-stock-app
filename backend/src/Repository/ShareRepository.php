<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Share;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ShareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Share::class);
    }

    /**
     * @return Share[]
     */
    public function listByUser(string $user): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.user = :user')
            ->orderBy('s.name')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findOneByUserAndName(string $user, string $name): ?Share
    {
        return $this->createQueryBuilder('s')
            ->where('s.user = :user')
            ->andWhere('s.name = :name')
            ->setParameters([
                'user' => $user,
                'name' => $name,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
