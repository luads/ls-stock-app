<?php

namespace App\Tests\Helper;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;
use LogicException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;

class DatabasePrimer
{
    public static function prime(KernelInterface $kernel, array $fixtures = []): void
    {
        if ('test' !== $kernel->getEnvironment()) {
            throw new LogicException('Primer must be executed in the test environment');
        }

        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metadatas);

        $fixtureLoader = new ContainerAwareLoader($kernel->getContainer());
        $fixtureExecutor = new ORMExecutor($entityManager, new ORMPurger($entityManager));

        array_walk($fixtures, function (FixtureInterface $fixture) use ($fixtureLoader) {
            $fixtureLoader->addFixture($fixture);
        });

        $fixtureExecutor->execute($fixtureLoader->getFixtures());
    }

    public static function truncateEntities(KernelInterface $kernel): void
    {
        if ('test' !== $kernel->getEnvironment()) {
            throw new LogicException('Teardown must be executed in the test environment');
        }

        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($entityManager);
        $purger->purge();
    }
}
