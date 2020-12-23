<?php

namespace App\Tests\Unit\Util;

use App\Entity\Agency;
use App\Entity\User;
use App\Exception\ConflictException;
use App\Factory\AgencyFactory;
use App\Model\Agency\CreateAgencyInput;
use App\Repository\AgencyRepository;
use App\Service\AgencyService;
use App\Service\NotificationService;
use App\Service\UserService;
use App\Util\AgencyHelper;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class AgencyServiceTest extends TestCase
{
    use ProphecyTrait;

    private AgencyService $agencyService;

    private $notificationService;
    private $userService;
    private $entityManager;
    private $agencyFactory;
    private $agencyHelper;
    private $agencyRepository;

    public function setUp(): void
    {
        $this->notificationService = $this->prophesize(NotificationService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->agencyFactory = $this->prophesize(AgencyFactory::class);
        $this->agencyHelper = $this->prophesize(AgencyHelper::class);
        $this->agencyRepository = $this->prophesize(AgencyRepository::class);

        $this->agencyService = new AgencyService(
            $this->notificationService->reveal(),
            $this->userService->reveal(),
            $this->entityManager->reveal(),
            $this->agencyFactory->reveal(),
            $this->agencyHelper->reveal(),
            $this->agencyRepository->reveal()
        );
    }

    public function testFindOrCreateByNameWhenNotExists(): void
    {
        $agencyName = 'Devon Homes';

        $this->agencyRepository->findOneBy(['name' => $agencyName])->shouldBeCalledOnce()->willReturn(null);

        $this->agencyHelper->generateSlug(Argument::type(Agency::class))->shouldBeCalledOnce();

        $this->entityManager->persist(Argument::type(Agency::class))->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledTimes(1);

        $result = $this->agencyService->findOrCreateByName($agencyName);

        $this->assertEquals($agencyName, $result->getName());
    }

    public function testFindOrCreateByNameWhenAlreadyExists(): void
    {
        $agencyName = 'Cornwall Homes';

        $agency = (new Agency())->setName($agencyName);

        $this->agencyRepository->findOneBy(['name' => $agencyName])->shouldBeCalledOnce()->willReturn($agency);

        $this->entityManager->flush()->shouldNotBeCalled();

        $result = $this->agencyService->findOrCreateByName($agencyName);

        $this->assertEquals($agencyName, $result->getName());
    }

    public function testCreateAgency(): void
    {
        $createAgencyInput = new CreateAgencyInput(
            'Test Agency Name',
            'https://test.com/welcome',
            null,
            null
        );
        $user = new User();
        $agency = new Agency();

        $this->userService->getEntityFromInterface($user)
            ->shouldBeCalledOnce()
            ->willReturn($user);

        $this->agencyFactory->createAgencyEntityFromCreateAgencyInputModel($createAgencyInput)
            ->shouldBeCalledOnce()
            ->willReturn($agency);

        $this->entityManager->persist($agency)->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledOnce();

        $this->notificationService->sendAgencyModerationNotification($agency)->shouldBeCalledOnce();

        $output = $this->agencyService->createAgency($createAgencyInput, $user);

        $this->assertContains($user, $agency->getAdminUsers());
        $this->assertEquals($user->getAdminAgency(), $agency);
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
        $this->entityManager->flush()->shouldNotBeCalled();

        $this->agencyService->createAgency($createAgencyInput, $user);
    }
}
