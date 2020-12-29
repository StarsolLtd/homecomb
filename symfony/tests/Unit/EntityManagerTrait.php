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
}
