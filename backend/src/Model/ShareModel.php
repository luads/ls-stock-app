<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Share;
use App\Repository\ShareRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;

class ShareModel
{
    private ManagerRegistry $registry;
    private ShareRepository $repository;

    public function __construct(ManagerRegistry $registry, ShareRepository $repository)
    {
        $this->registry = $registry;
        $this->repository = $repository;
    }

    public function createOrUpdate(string $user, string $name, int $quantity): Share
    {
        $share = $this->repository->findOneByUserAndName($user, $name);

        $share ??= (new Share())
            ->setUser($user)
            ->setName($name);

        $newQuantity = $share->getQuantity() + $quantity;

        $share->setQuantity($newQuantity)
            ->setUpdatedAt(new DateTime());

        $this->registry->getManager()->persist($share);
        $this->registry->getManager()->flush();

        return $share;
    }
}
