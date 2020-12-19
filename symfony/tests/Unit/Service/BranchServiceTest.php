<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\User;
use App\Factory\BranchFactory;
use App\Model\Branch\CreateBranchInput;
use App\Repository\AgencyRepository;
use App\Repository\BranchRepository;
use App\Service\BranchService;
use App\Service\NotificationService;
use App\Service\UserService;
use App\Util\BranchHelper;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class BranchServiceTest extends TestCase
{
    use ProphecyTrait;

    private BranchService $branchService;

    private $notificationService;
    private $userService;
    private $entityManager;
    private $branchFactory;
    private $branchHelper;
    private $branchRepository;

    public function setUp(): void
    {
        $this->notificationService = $this->prophesize(NotificationService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->agencyRepository = $this->prophesize(AgencyRepository::class);
        $this->branchFactory = $this->prophesize(BranchFactory::class);
        $this->branchHelper = $this->prophesize(BranchHelper::class);
        $this->branchRepository = $this->prophesize(BranchRepository::class);

        $this->branchService = new BranchService(
            $this->notificationService->reveal(),
            $this->userService->reveal(),
            $this->entityManager->reveal(),
            $this->agencyRepository->reveal(),
            $this->branchFactory->reveal(),
            $this->branchHelper->reveal(),
            $this->branchRepository->reveal()
        );
    }

    public function testCreateBranch(): void
    {
        $createBranchInput = new CreateBranchInput(
            'Test Branch Name',
            '0700 100 200',
            null,
            'sample'
        );
        $agency = new Agency();
        $user = (new User())->setAdminAgency($agency);
        $branch = new Branch();

        $this->userService->getEntityFromInterface($user)
            ->shouldBeCalledOnce()
            ->willReturn($user);

        $this->branchFactory->createBranchEntityFromCreateBranchInputModel($createBranchInput, $agency)
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $this->entityManager->persist($branch)->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledOnce();

        $this->notificationService->sendBranchModerationNotification($branch)->shouldBeCalledOnce();

        $output = $this->branchService->createBranch($createBranchInput, $user);

        $this->assertTrue($output->isSuccess());
    }
}
