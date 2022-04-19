<?php

namespace App\Tests\Unit\Service\Branch;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\User;
use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Factory\BranchFactory;
use App\Model\Branch\CreateInputInterface;
use App\Repository\BranchRepositoryInterface;
use App\Service\Branch\CreateService;
use App\Service\NotificationService;
use App\Service\User\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class CreateServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;
    use UserEntityFromInterfaceTrait;

    private CreateService $createService;

    private ObjectProphecy $notificationService;
    private ObjectProphecy $branchFactory;
    private ObjectProphecy $branchRepository;

    public function setUp(): void
    {
        $this->notificationService = $this->prophesize(NotificationService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->branchFactory = $this->prophesize(BranchFactory::class);
        $this->branchRepository = $this->prophesize(BranchRepositoryInterface::class);

        $this->createService = new CreateService(
            $this->notificationService->reveal(),
            $this->userService->reveal(),
            $this->entityManager->reveal(),
            $this->branchFactory->reveal(),
            $this->branchRepository->reveal()
        );
    }

    public function testCreateBranch1(): void
    {
        $input = $this->prophesize(CreateInputInterface::class);

        $agency = $this->prophesize(Agency::class);
        $branch = $this->prophesize(Branch::class);
        $user = $this->prophesize(User::class);

        $this->assertGetUserEntityFromInterface($user);

        $user->getAdminAgency()->shouldBeCalledOnce()->willReturn($agency);

        $input->getBranchName()->shouldBeCalledOnce()->willReturn('Blakeney');

        $this->branchRepository->findOneByNameAndAgencyOrNull('Blakeney', $agency)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->branchFactory->createEntityFromCreateBranchInput($input, $agency)
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $this->assertEntitiesArePersistedAndFlush([$branch]);

        $this->notificationService->sendBranchModerationNotification($branch)->shouldBeCalledOnce();

        $output = $this->createService->createBranch($input->reveal(), $user->reveal());

        $this->assertTrue($output->isSuccess());
    }

    /**
     * Test createBranch throws a ConflictException if it already exists.
     */
    public function testCreateBranch2(): void
    {
        $input = $this->prophesize(CreateInputInterface::class);

        $agency = $this->prophesize(Agency::class);
        $user = $this->prophesize(User::class);
        $existingBranch = $this->prophesize(Branch::class);

        $this->assertGetUserEntityFromInterface($user);

        $user->getAdminAgency()->shouldBeCalledOnce()->willReturn($agency);

        $input->getBranchName()->shouldBeCalledOnce()->willReturn('Blakeney');

        $this->branchRepository->findOneByNameAndAgencyOrNull('Blakeney', $agency)
            ->shouldBeCalledOnce()
            ->willReturn($existingBranch);

        $this->expectException(ConflictException::class);
        $this->expectExceptionMessage('A branch with the name Blakeney already exists for this agency.');

        $this->assertEntityManagerUnused();

        $this->createService->createBranch($input->reveal(), $user->reveal());
    }

    /**
     * Test createBranch throws a ForbiddenException is the user is not agency admin.
     */
    public function testCreateBranchThrowsForbiddenExceptionIfUserNotAgencyAdmin(): void
    {
        $input = $this->prophesize(CreateInputInterface::class);
        $user = $this->prophesize(User::class);

        $this->assertGetUserEntityFromInterface($user);

        $user->getAdminAgency()->shouldBeCalledOnce()->willReturn(null);

        $user->getUsername()->shouldBeCalledOnce()->willReturn('not.agency.admin@starsol.co.uk');

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Logged in user not.agency.admin@starsol.co.uk is not the admin of an agency.');

        $this->assertEntityManagerUnused();

        $this->createService->createBranch($input->reveal(), $user->reveal());
    }
}
