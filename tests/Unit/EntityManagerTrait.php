<?php

namespace App\Tests\Unit;

use Prophecy\Argument;

trait EntityManagerTrait
{
    private $entityManager;

    private function assertEntityManagerUnused(): void
    {
        $this->entityManager->persist(Argument::any())
            ->shouldNotBeCalled();

        $this->entityManager->flush()
            ->shouldNotBeCalled();
    }

    /**
     * @param object[] $entities
     */
    private function assertEntitiesArePersistedAndFlush(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->entityManager->persist($entity)->shouldBeCalledOnce();
        }
        $this->assertFlush();
    }

    private function assertFlush(): void
    {
        $this->entityManager->flush()->shouldBeCalledOnce();
    }
}
