<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\User;
use App\Exception\NotFoundException;
use App\Factory\FlatModelFactory;
use App\Model\Agency\Flat;
use App\Service\AgencyService;
use App\Service\User\UserService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\AgencyService
 */
final class AgencyServiceTest extends TestCase
{
    use ProphecyTrait;

    private AgencyService $agencyService;

    private ObjectProphecy $userService;
    private ObjectProphecy $flatModelFactory;

    public function setUp(): void
    {
        $this->userService = $this->prophesize(UserService::class);
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);

        $this->agencyService = new AgencyService(
            $this->userService->reveal(),
            $this->flatModelFactory->reveal(),
        );
    }

    /**
     * @covers \App\Service\AgencyService::getAgencyForUser
     */
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

        $this->agencyService->getAgencyForUser($user);
    }

    /**
     * @covers \App\Service\AgencyService::getAgencyForUser
     * Test throws NotFoundException when user is not an agency admin
     */
    public function testGetAgencyForUser2(): void
    {
        $user = new User();

        $this->userService->getEntityFromInterface($user)
            ->shouldBeCalledOnce()
            ->willReturn($user);

        $this->expectException(NotFoundException::class);

        $this->agencyService->getAgencyForUser($user);
    }
}
