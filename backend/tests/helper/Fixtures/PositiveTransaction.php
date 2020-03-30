<?php

declare(strict_types=1);

namespace App\Tests\Helper\Fixtures;

use App\Entity\Transaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PositiveTransaction extends Fixture
{
    public const REFERENCE = 'positive-transaction';
    public const USER = 'positive-user-test';

    public function load(ObjectManager $manager): void
    {
        $transaction = (new Transaction())
            ->setUser(self::USER)
            ->setBalance(80.5);

        $manager->persist($transaction);
        $manager->flush();

        $this->addReference(self::REFERENCE, $transaction);
    }
}
