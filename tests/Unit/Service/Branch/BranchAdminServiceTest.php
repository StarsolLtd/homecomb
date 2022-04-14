<?php

namespace App\Tests\Unit\Service\Branch;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\User;
use App\Repository\BranchRepositoryInterface;
use App\Service\Branch\BranchAdminService;
use App\Service\User\UserService;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\Branch\BranchAdminTest
 */
final class BranchAdminServiceTest extends TestCase
{
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private BranchAdminService $branchAdminService;

    private ObjectProphecy $branchRepository;
    private ObjectProphecy $userService;

    public function setUp(): void
    {
        $this->branchRepository = $this->prophesize(BranchRepositoryInterface::class);
        $this->userService = $this->prophesize(UserService::class);

        $this->branchAdminService = new BranchAdminService(
            $this->branchRepository->reveal(),
            $this->userService->reveal(),
        );
    }

    /**
     * @covers \App\Service\Branch\BranchAdminTest::isUserBranchAdmin
     * Test returns true when the user's admin agency ID matches the branch's agency ID
     */
    public function testIsUserBranchAdmin1(): void
    {
        $user = $this->prophesize(User::class);
        $branch = $this->prophesize(Branch::class);
        $agency = $this->prophesize(Agency::class);
        $branchAgency = $this->prophesize(Agency::class);

        $user->getAdminAgency()->shouldBeCalled()->willReturn($agency);
        $branch->getAgency()->shouldBeCalled()->willReturn($branchAgency);
        $branchAgency->getId()->shouldBeCalled()->willReturn(42);
        $agency->getId()->shouldBeCalled()->willReturn(42);

        $this->branchRepository->findOnePublishedBySlug('branchslug')
            ->shouldBeCalled()
            ->willReturn($branch);

        $this->assertGetUserEntityFromInterface($user);

        $output = $this->branchAdminService->isUserBranchAdmin('branchslug', $user->reveal());

        $this->assertTrue($output);
    }

    /**
     * @covers \App\Service\Branch\BranchAdminTest::isUserBranchAdmin
     * Test returns false when the user's admin agency ID does not match the branch's agency ID
     */
    public function testIsUserBranchAdmin2(): void
    {
        $user = $this->prophesize(User::class);
        $branch = $this->prophesize(Branch::class);
        $agency = $this->prophesize(Agency::class);
        $branchAgency = $this->prophesize(Agency::class);

        $user->getAdminAgency()->shouldBeCalled()->willReturn($agency);
        $branch->getAgency()->shouldBeCalled()->willReturn($branchAgency);
        $branchAgency->getId()->shouldBeCalled()->willReturn(42);
        $agency->getId()->shouldBeCalled()->willReturn(88);

        $this->branchRepository->findOnePublishedBySlug('branchslug')
            ->shouldBeCalled()
            ->willReturn($branch);

        $this->assertGetUserEntityFromInterface($user);

        $output = $this->branchAdminService->isUserBranchAdmin('branchslug', $user->reveal());

        $this->assertFalse($output);
    }

    /**
     * @covers \App\Service\Branch\BranchAdminTest::isUserBranchAdmin
     * Test returns false when the user is not an agency admin
     */
    public function testIsUserBranchAdmin3(): void
    {
        $user = $this->prophesize(User::class);

        $user->getAdminAgency()->shouldBeCalled()->willReturn(null);

        $this->assertGetUserEntityFromInterface($user);

        $output = $this->branchAdminService->isUserBranchAdmin('branchslug', $user->reveal());

        $this->assertFalse($output);
    }

    /**
     * @covers \App\Service\Branch\BranchAdminTest::isUserBranchAdmin
     * Test returns false when the branch is not associated with an agency
     */
    public function testIsUserBranchAdmin4(): void
    {
        $user = $this->prophesize(User::class);
        $branch = $this->prophesize(Branch::class);
        $agency = $this->prophesize(Agency::class);

        $user->getAdminAgency()->shouldBeCalled()->willReturn($agency);
        $branch->getAgency()->shouldBeCalled()->willReturn(null);

        $this->branchRepository->findOnePublishedBySlug('branchslug')
            ->shouldBeCalled()
            ->willReturn($branch);

        $this->assertGetUserEntityFromInterface($user);

        $output = $this->branchAdminService->isUserBranchAdmin('branchslug', $user->reveal());

        $this->assertFalse($output);
    }
}
