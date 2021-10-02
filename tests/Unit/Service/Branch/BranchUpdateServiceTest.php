<?php

namespace App\Tests\Unit\Service\Branch;

use App\Entity\Branch;
use App\Entity\User;
use App\Model\Branch\UpdateBranchInput;
use App\Repository\BranchRepository;
use App\Service\Branch\BranchUpdateService;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\Branch\BranchUpdateService
 */
final class BranchUpdateServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;
    use UserEntityFromInterfaceTrait;

    private BranchUpdateService $branchService;

    private ObjectProphecy $entityManager;
    private ObjectProphecy $branchRepository;

    public function setUp(): void
    {
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->branchRepository = $this->prophesize(BranchRepository::class);

        $this->branchService = new BranchUpdateService(
            $this->userService->reveal(),
            $this->entityManager->reveal(),
            $this->branchRepository->reveal()
        );
    }

    /**
     * @covers \App\Service\Branch\BranchUpdateService::updateBranch
     */
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
}
