<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\User;
use App\Exception\NotFoundException;
use App\Factory\AgencyAdminFactory;
use App\Factory\FlatModelFactory;
use App\Model\AgencyAdmin\Home;
use App\Model\Branch\Flat;
use App\Repository\BranchRepository;
use App\Service\AgencyAdminService;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\AgencyAdminService
 */
final class AgencyAdminServiceTest extends TestCase
{
    use ProphecyTrait;

    private AgencyAdminService $agencyAdminService;

    private ObjectProphecy $userService;
    private ObjectProphecy $agencyAdminFactory;
    private ObjectProphecy $flatModelFactory;
    private ObjectProphecy $branchRepository;

    public function setUp(): void
    {
        $this->userService = $this->prophesize(UserService::class);
        $this->agencyAdminFactory = $this->prophesize(AgencyAdminFactory::class);
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);
        $this->branchRepository = $this->prophesize(BranchRepository::class);

        $this->agencyAdminService = new AgencyAdminService(
            $this->userService->reveal(),
            $this->agencyAdminFactory->reveal(),
            $this->flatModelFactory->reveal(),
            $this->branchRepository->reveal(),
        );
    }

    /**
     * @covers \App\Service\AgencyAdminService::getHomeForUser
     */
    public function testGetHomeForUser1(): void
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

    /**
     * @covers \App\Service\AgencyAdminService::getHomeForUser
     * Test throws NotFoundException is user is not an agency admin
     */
    public function testGetHomeForUser2(): void
    {
        $user = new User();

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->expectException(NotFoundException::class);

        $this->agencyAdminService->getHomeForUser($user);
    }

    /**
     * @covers \App\Service\AgencyAdminService::getBranch
     */
    public function testGetBranch1(): void
    {
        $user = new User();
        $branch = new Branch();
        $branchModel = $this->prophesize(Flat::class);
        $branchSlug = 'testbranch';

        $this->userService->getEntityFromInterface($user)
            ->shouldBeCalledOnce()
            ->willReturn($user);

        $this->branchRepository->findOneBySlugUserCanManage($branchSlug, $user)
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $this->flatModelFactory->getBranchFlatModel($branch)
            ->shouldBeCalledOnce()
            ->willReturn($branchModel);

        $output = $this->agencyAdminService->getBranch($branchSlug, $user);

        $this->assertEquals($branchModel->reveal(), $output);
    }
}
