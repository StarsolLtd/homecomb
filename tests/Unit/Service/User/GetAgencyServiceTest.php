<?php

namespace App\Tests\Unit\Service\User;

use App\Entity\Agency;
use App\Entity\User;
use App\Exception\NotFoundException;
use App\Factory\FlatModelFactory;
use App\Model\Agency\Flat;
use App\Service\User\GetAgencyService;
use App\Service\User\UserService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class GetAgencyServiceTest extends TestCase
{
    use ProphecyTrait;

    private GetAgencyService $getAgencyService;

    private ObjectProphecy $userService;
    private ObjectProphecy $flatModelFactory;

    public function setUp(): void
    {
        $this->userService = $this->prophesize(UserService::class);
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);

        $this->getAgencyService = new GetAgencyService(
            $this->userService->reveal(),
            $this->flatModelFactory->reveal(),
        );
    }

    public function testGetAgencyForUser1(): void
    {
        $slug = 'testagencyslug';
        $user = new User();
        $agency = (new Agency())->setSlug($slug)->addAdminUser($user);
        $agencyModel = $this->prophesize(Flat::class);

        $this->userService->getEntityFromInterface($user)
            ->shouldBeCalledOnce()
            ->willReturn($user);

        $this->flatModelFactory->getAgencyFlatModel($agency)
            ->shouldBeCalledOnce()
            ->willReturn($agencyModel);

        $this->getAgencyService->getAgencyForUser($user);
    }

    /**
     * Test throws NotFoundException when user is not an agency admin.
     */
    public function testGetAgencyForUser2(): void
    {
        $user = new User();

        $this->userService->getEntityFromInterface($user)
            ->shouldBeCalledOnce()
            ->willReturn($user);

        $this->expectException(NotFoundException::class);

        $this->getAgencyService->getAgencyForUser($user);
    }
}
