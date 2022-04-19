<?php

namespace App\Tests\Unit\Service\Branch;

use App\Entity\Branch;
use App\Entity\User;
use App\Model\Branch\UpdateInputInterface;
use App\Repository\BranchRepositoryInterface;
use App\Service\Branch\UpdateService;
use App\Service\User\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class UpdateServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;
    use UserEntityFromInterfaceTrait;

    private UpdateService $branchService;

    private ObjectProphecy $branchRepository;

    public function setUp(): void
    {
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->branchRepository = $this->prophesize(BranchRepositoryInterface::class);

        $this->branchService = new UpdateService(
            $this->userService->reveal(),
            $this->entityManager->reveal(),
            $this->branchRepository->reveal()
        );
    }

    public function testUpdateBranch(): void
    {
        $slug = 'testbranchslug';

        $input = $this->prophesize(UpdateInputInterface::class);
        $input->getTelephone()->shouldBeCalledOnce()->willReturn('0555 555 555');
        $input->getEmail()->shouldBeCalledOnce()->willReturn('updated.branch@starsol.co.uk');

        $user = $this->prophesize(User::class);
        $branch = $this->prophesize(Branch::class);

        $this->assertGetUserEntityFromInterface($user);

        $this->branchRepository->findOneBySlugUserCanManage('testbranchslug', $user)
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $branch->setTelephone('0555 555 555')->shouldBeCalledOnce()->willReturn($branch);
        $branch->setEmail('updated.branch@starsol.co.uk')->shouldBeCalledOnce()->willReturn($branch);

        $this->entityManager->flush()->shouldBeCalledOnce();

        $output = $this->branchService->updateBranch($slug, $input->reveal(), $user->reveal());

        $this->assertTrue($output->isSuccess());
    }
}
