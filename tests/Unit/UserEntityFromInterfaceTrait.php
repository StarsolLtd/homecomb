<?php

namespace App\Tests\Unit;

use Prophecy\Prophecy\ObjectProphecy;

trait UserEntityFromInterfaceTrait
{
    private ObjectProphecy $userService;

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
