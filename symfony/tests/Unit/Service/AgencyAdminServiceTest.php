<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\User;
use App\Factory\AgencyAdminFactory;
use App\Factory\FlatModelFactory;
use App\Model\AgencyAdmin\Home;
use App\Model\Branch\Flat;
use App\Repository\AgencyRepository;
use App\Repository\BranchRepository;
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
    private $flatModelFactory;
    private $agencyRepository;
    private $branchRepository;

    public function setUp(): void
    {
        $this->userService = $this->prophesize(UserService::class);
        $this->agencyAdminFactory = $this->prophesize(AgencyAdminFactory::class);
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);
        $this->agencyRepository = $this->prophesize(AgencyRepository::class);
        $this->branchRepository = $this->prophesize(BranchRepository::class);

        $this->agencyAdminService = new AgencyAdminService(
            $this->userService->reveal(),
            $this->agencyAdminFactory->reveal(),
            $this->flatModelFactory->reveal(),
            $this->agencyRepository->reveal(),
            $this->branchRepository->reveal(),
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

    public function testGetBranch(): void
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
