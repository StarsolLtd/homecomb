<?php

namespace App\Tests\Unit\Service\Agency;

use App\Entity\Agency;
use App\Entity\User;
use App\Exception\ConflictException;
use App\Factory\AgencyFactory;
use App\Model\Agency\CreateAgencyInput;
use App\Model\Agency\CreateAgencyInputInterface;
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

    public function testCreateAgency(): void
    {
        $createInput = $this->prophesize(CreateAgencyInputInterface::class);
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

    public function testCreateAgencyThrowsConflictExceptionWhenUserIsAlreadyAgencyAdmin(): void
    {
        $createAgencyInput = new CreateAgencyInput(
            'Test Agency Name',
            'https://test.com/welcome',
            null,
            null
        );
        $agency = new Agency();
        $user = (new User())->setAdminAgency($agency);

        $this->userService->getEntityFromInterface($user)
            ->shouldBeCalledOnce()
            ->willReturn($user);

        $this->expectException(ConflictException::class);
        $this->assertEntityManagerUnused();

        $this->createService->createAgency($createAgencyInput, $user);
    }
}
