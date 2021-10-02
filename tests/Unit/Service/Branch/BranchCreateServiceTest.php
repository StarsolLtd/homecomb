<?php

namespace App\Tests\Unit\Service\Branch;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\User;
use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Factory\BranchFactory;
use App\Model\Branch\CreateBranchInput;
use App\Repository\BranchRepository;
use App\Service\Branch\BranchCreateService;
use App\Service\NotificationService;
use App\Service\User\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\Branch\BranchCreateService
 */
final class BranchCreateServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;
    use UserEntityFromInterfaceTrait;

    private BranchCreateService $branchCreateService;

    private ObjectProphecy $notificationService;
    private ObjectProphecy $entityManager;
    private ObjectProphecy $branchFactory;
    private ObjectProphecy $branchRepository;

    public function setUp(): void
    {
        $this->notificationService = $this->prophesize(NotificationService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->branchFactory = $this->prophesize(BranchFactory::class);
        $this->branchRepository = $this->prophesize(BranchRepository::class);

        $this->branchCreateService = new BranchCreateService(
            $this->notificationService->reveal(),
            $this->userService->reveal(),
            $this->entityManager->reveal(),
            $this->branchFactory->reveal(),
            $this->branchRepository->reveal()
        );
    }

    /**
     * @covers \App\Service\Branch\BranchCreateService::createBranch
     */
    public function testCreateBranch(): void
    {
        $createBranchInput = $this->getValidCreateBranchInput();
        $agency = new Agency();
        $user = (new User())->setAdminAgency($agency);
        $branch = new Branch();

        $this->assertGetUserEntityFromInterface($user);

        $this->branchRepository->findOneByNameAndAgencyOrNull($createBranchInput->getBranchName(), $agency)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->branchFactory->createEntityFromCreateBranchInput($createBranchInput, $agency)
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $this->entityManager->persist($branch)->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledOnce();

        $this->notificationService->sendBranchModerationNotification($branch)->shouldBeCalledOnce();

        $output = $this->branchCreateService->createBranch($createBranchInput, $user);

        $this->assertTrue($output->isSuccess());
    }

    /**
     * @covers \App\Service\Branch\BranchCreateService::createBranch
     */
    public function testCreateBranchThrowsConflictExceptionIfAlreadyExists(): void
    {
        $createBranchInput = $this->getValidCreateBranchInput();
        $agency = new Agency();
        $user = (new User())->setAdminAgency($agency);
        $existingBranch = new Branch();

        $this->assertGetUserEntityFromInterface($user);

        $this->branchRepository->findOneByNameAndAgencyOrNull($createBranchInput->getBranchName(), $agency)
            ->shouldBeCalledOnce()
            ->willReturn($existingBranch);

        $this->expectException(ConflictException::class);

        $this->assertEntityManagerUnused();

        $this->branchCreateService->createBranch($createBranchInput, $user);
    }

    /**
     * @covers \App\Service\Branch\BranchCreateService::createBranch
     */
    public function testCreateBranchThrowsForbiddenExceptionIfUserNotAgencyAdmin(): void
    {
        $createBranchInput = $this->getValidCreateBranchInput();
        $user = (new User())->setEmail('not.agency.admin@starsol.co.uk');

        $this->assertGetUserEntityFromInterface($user);

        $this->expectException(ForbiddenException::class);

        $this->assertEntityManagerUnused();

        $this->branchCreateService->createBranch($createBranchInput, $user);
    }

    private function getValidCreateBranchInput(): CreateBranchInput
    {
        return new CreateBranchInput(
            'Blakeney',
            '0700 100 200',
            null,
            'sample'
        );
    }
}
