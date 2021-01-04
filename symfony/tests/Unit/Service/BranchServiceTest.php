<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\User;
use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Factory\BranchFactory;
use App\Model\Branch\Branch as BranchModel;
use App\Model\Branch\CreateBranchInput;
use App\Model\Branch\UpdateBranchInput;
use App\Model\Branch\View;
use App\Repository\AgencyRepository;
use App\Repository\BranchRepository;
use App\Service\BranchService;
use App\Service\NotificationService;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use App\Util\BranchHelper;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class BranchServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;
    use UserEntityFromInterfaceTrait;

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
        $createBranchInput = $this->getValidCreateBranchInput();
        $agency = new Agency();
        $user = (new User())->setAdminAgency($agency);
        $branch = new Branch();

        $this->assertGetUserEntityFromInterface($user);

        $this->branchRepository->findOneByNameAndAgencyOrNull($createBranchInput->getBranchName(), $agency)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->branchFactory->createBranchEntityFromCreateBranchInputModel($createBranchInput, $agency)
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $this->entityManager->persist($branch)->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledOnce();

        $this->notificationService->sendBranchModerationNotification($branch)->shouldBeCalledOnce();

        $output = $this->branchService->createBranch($createBranchInput, $user);

        $this->assertTrue($output->isSuccess());
    }

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

        $this->branchService->createBranch($createBranchInput, $user);
    }

    public function testCreateBranchThrowsForbiddenExceptionIfUserNotAgencyAdmin(): void
    {
        $createBranchInput = $this->getValidCreateBranchInput();
        $user = (new User())->setEmail('not.agency.admin@starsol.co.uk');

        $this->assertGetUserEntityFromInterface($user);

        $this->expectException(ForbiddenException::class);

        $this->assertEntityManagerUnused();

        $this->branchService->createBranch($createBranchInput, $user);
    }

    public function testUpdateBranch(): void
    {
        $slug = 'testbranchslug';
        $updateBranchInput = new UpdateBranchInput(
            '0555 555 555',
            'updated.branch@starsol.co.uk',
            'SAMPLE'
        );

        $user = new User();
        $branch = new Branch();

        $this->assertGetUserEntityFromInterface($user);

        $this->branchRepository->findOneBySlugUserCanManage('testbranchslug', $user)
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $this->entityManager->flush()->shouldBeCalledOnce();

        $output = $this->branchService->updateBranch($slug, $updateBranchInput, $user);

        $this->assertEquals('0555 555 555', $branch->getTelephone());
        $this->assertEquals('updated.branch@starsol.co.uk', $branch->getEmail());
        $this->assertTrue($output->isSuccess());
    }

    public function testGetViewBySlug(): void
    {
        $branch = (new Branch());
        $view = (new View(new BranchModel('branchslug', 'Test Branch Name', null, null), null, []));

        $this->branchRepository->findOnePublishedBySlug('branchslug')
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $this->branchFactory->createViewFromEntity($branch)
            ->shouldBeCalledOnce()
            ->willReturn($view);

        $view = $this->branchService->getViewBySlug('branchslug');

        $this->assertEquals('branchslug', $view->getBranch()->getSlug());
        $this->assertEquals('Test Branch Name', $view->getBranch()->getName());
        $this->assertNull($view->getAgency());
    }

    public function testFindOrCreateWithoutAgencyWhereBranchAlreadyExists(): void
    {
        $branch = new Branch();

        $this->branchRepository->findOneByNameWithoutAgencyOrNull('Test Name', null)
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $output = $this->branchService->findOrCreate('Test Name', null);

        $this->assertEquals($branch, $output);
        $this->assertEntityManagerUnused();
    }

    public function testFindOrCreateWhereBranchAlreadyExists(): void
    {
        $branch = new Branch();
        $agency = new Agency();

        $this->branchRepository->findOneByNameAndAgencyOrNull('Test Name', $agency)
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $output = $this->branchService->findOrCreate('Test Name', $agency);

        $this->assertEquals($branch, $output);
        $this->assertEntityManagerUnused();
    }

    public function testFindOrCreateWhereBranchDoesNotExists(): void
    {
        $agency = new Agency();

        $this->branchRepository->findOneByNameAndAgencyOrNull('Test Name', $agency)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->branchHelper->generateSlug(Argument::type(Branch::class))
            ->shouldBeCalledOnce()
            ->willReturn('testslug');

        $this->entityManager->persist(Argument::type(Branch::class))->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledOnce();

        $output = $this->branchService->findOrCreate('Test Name', $agency);

        $this->assertEquals('Test Name', $output->getName());
        $this->assertEquals($agency, $output->getAgency());
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
