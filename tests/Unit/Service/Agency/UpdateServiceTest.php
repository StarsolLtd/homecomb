<?php

namespace App\Tests\Unit\Service\Agency;

use App\Entity\Agency;
use App\Entity\User;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Model\Agency\UpdateInputInterface;
use App\Service\Agency\UpdateService;
use App\Service\User\UserService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class UpdateServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private const TEST_SLUG = 'test-agency-slug';

    private UpdateService $updateService;

    private ObjectProphecy $userService;

    public function setUp(): void
    {
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);

        $this->updateService = new UpdateService(
            $this->userService->reveal(),
            $this->entityManager->reveal(),
        );
    }

    public function testUpdateAgency1(): void
    {
        $input = $this->prophesize(UpdateInputInterface::class);

        $user = $this->prophesize(User::class);
        $agency = $this->prophesize(Agency::class);

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $user->getAdminAgency()->shouldBeCalledOnce()->willReturn($agency);

        $agency->getSlug()->shouldBeCalledOnce()->willReturn(self::TEST_SLUG);

        $input->getExternalUrl()->shouldBeCalledOnce()->willReturn('https://updated.com/here');
        $input->getPostcode()->shouldBeCalledOnce()->willReturn('NR21 4SF');

        $agency->setExternalUrl('https://updated.com/here')->shouldBeCalledOnce()->willReturn($agency);
        $agency->setPostcode('NR21 4SF')->shouldBeCalledOnce()->willReturn($agency);

        $this->entityManager->flush()->shouldBeCalledOnce();

        $output = $this->updateService->updateAgency(self::TEST_SLUG, $input->reveal(), $user->reveal());

        $this->assertTrue($output->isSuccess());
    }

    /**
     * Test updateAgency throws exception when user is not agency admin.
     */
    public function testUpdateAgency2(): void
    {
        $input = $this->prophesize(UpdateInputInterface::class);

        $user = $this->prophesize(User::class);

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->expectException(NotFoundException::class);
        $this->assertEntityManagerUnused();

        $this->updateService->updateAgency(self::TEST_SLUG, $input->reveal(), $user->reveal());
    }

    /**
     * Test update agency throws exception when user is admin of different agency.
     */
    public function testUpdateAgency3(): void
    {
        $input = $this->prophesize(UpdateInputInterface::class);
        $user = $this->prophesize(User::class);
        $agency = $this->prophesize(Agency::class);

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $user->getAdminAgency()->shouldBeCalledOnce()->willReturn($agency);

        $agency->getSlug()->shouldBeCalledOnce()->willReturn(self::TEST_SLUG);

        $this->expectException(ForbiddenException::class);
        $this->assertEntityManagerUnused();

        $this->updateService->updateAgency('different', $input->reveal(), $user->reveal());
    }
}
