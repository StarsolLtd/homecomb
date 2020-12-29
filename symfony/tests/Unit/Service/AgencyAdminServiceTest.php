<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\User;
use App\Factory\AgencyAdminFactory;
use App\Model\AgencyAdmin\Home;
use App\Repository\AgencyRepository;
use App\Service\AgencyAdminService;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class AgencyAdminServiceTest extends TestCase
{
    use ProphecyTrait;

    private AgencyAdminService $agencyAdminService;

    private $userService;
    private $agencyAdminFactory;
    private $agencyRepository;

    public function setUp(): void
    {
        $this->userService = $this->prophesize(UserService::class);
        $this->agencyAdminFactory = $this->prophesize(AgencyAdminFactory::class);
        $this->agencyRepository = $this->prophesize(AgencyRepository::class);

        $this->agencyAdminService = new AgencyAdminService(
            $this->userService->reveal(),
            $this->agencyAdminFactory->reveal(),
            $this->agencyRepository->reveal()
        );
    }

    public function testGetHomeForUser(): void
    {
        $user = new User();
        $agency = (new Agency())->addAdminUser($user);
        $home = $this->prophesize(Home::class);

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->agencyAdminFactory->getHome($agency)
            ->shouldBeCalledOnce()
            ->willReturn($home);

        $output = $this->agencyAdminService->getHomeForUser($user);

        $this->assertEquals($home->reveal(), $output);
    }
}
