<?php

namespace App\Tests\Unit;

trait UserEntityFromInterfaceTrait
{
    private $userService;

    private function assertGetUserEntityFromInterface($user): void
    {
        $this->userService->getEntityFromInterface($user)
            ->shouldBeCalledOnce()
            ->willReturn($user);
    }

    private function assertGetUserEntityOrNullFromInterface($user): void
    {
        $this->userService->getUserEntityOrNullFromUserInterface($user)
            ->shouldBeCalledOnce()
            ->willReturn($user);
    }
}
