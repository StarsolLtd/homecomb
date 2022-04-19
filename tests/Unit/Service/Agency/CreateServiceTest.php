<?php

namespace App\Tests\Unit\Service\Agency;

use App\Entity\Agency;
use App\Entity\User;
use App\Exception\ConflictException;
use App\Factory\AgencyFactory;
use App\Model\Agency\CreateInputInterface;
use App\Service\Agency\CreateService;
use App\Service\NotificationService;
use App\Service\User\UserService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class CreateServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private CreateService $createService;

    private ObjectProphecy $notificationService;
    private ObjectProphecy $userService;
    private ObjectProphecy $agencyFactory;

    public function setUp(): void
    {
        $this->notificationService = $this->prophesize(NotificationService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->agencyFactory = $this->prophesize(AgencyFactory::class);

        $this->createService = new CreateService(
            $this->notificationService->reveal(),
            $this->userService->reveal(),
            $this->entityManager->reveal(),
            $this->agencyFactory->reveal(),
        );
    }

    public function testCreateAgency1(): void
    {
        $createInput = $this->prophesize(CreateInputInterface::class);
        $user = $this->prophesize(User::class);
        $agency = $this->prophesize(Agency::class);

        $this->userService->getEntityFromInterface($user)
            ->shouldBeCalledOnce()
            ->willReturn($user);

        $this->agencyFactory->createAgencyEntityFromCreateAgencyInputModel($createInput)
            ->shouldBeCalledOnce()
            ->willReturn($agency);

        $agency->addAdminUser($user)->shouldBeCalledOnce();

        $this->entityManager->persist($agency)->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledOnce();

        $this->notificationService->sendAgencyModerationNotification($agency)->shouldBeCalledOnce();

        $output = $this->createService->createAgency($createInput->reveal(), $user->reveal());

        $this->assertTrue($output->isSuccess());
    }

    /**
     * Test createAgency method throws a ConflictException when the user is already an agency admin.
     */
    public function testCreateAgency2(): void
    {
        $input = $this->prophesize(CreateInputInterface::class);
        $user = $this->prophesize(User::class);
        $agency = $this->prophesize(Agency::class);

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $user->getAdminAgency()->shouldBeCalledOnce()->willReturn($agency);

        $this->expectException(ConflictException::class);
        $this->assertEntityManagerUnused();

        $this->createService->createAgency($input->reveal(), $user->reveal());
    }
}
