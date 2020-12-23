<?php

namespace App\Tests\Unit\Util;

use App\Entity\Agency;
use App\Entity\User;
use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Factory\AgencyFactory;
use App\Model\Agency\CreateAgencyInput;
use App\Model\Agency\UpdateAgencyInput;
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

    public function testUpdateAgency(): void
    {
        $slug = 'testagencyslug';
        $updateAgencyInput = new UpdateAgencyInput(
            'https://updated.com/here',
            'NR21 4SF',
            'SAMPLE'
        );

        $user = new User();
        $agency = (new Agency())->setSlug($slug)->addAdminUser($user);

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->entityManager->flush()->shouldBeCalledOnce();

        $output = $this->agencyService->updateAgency($slug, $updateAgencyInput, $user);

        $this->assertEquals('https://updated.com/here', $agency->getExternalUrl());
        $this->assertEquals('NR21 4SF', $agency->getPostcode());
        $this->assertTrue($output->isSuccess());
    }

    public function testUpdateAgencyThrowsExceptionWhenUserNotAgencyAdmin(): void
    {
        $slug = 'testagencyslug';
        $updateAgencyInput = new UpdateAgencyInput(
            'https://updated.com/here',
            'NR21 4SF',
            'SAMPLE'
        );

        $user = new User();

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->expectException(NotFoundException::class);
        $this->entityManager->flush()->shouldNotBeCalled();

        $this->agencyService->updateAgency($slug, $updateAgencyInput, $user);
    }

    public function testUpdateAgencyThrowsExceptionWhenUserAdminOfDifferentAgency(): void
    {
        $slug = 'testagencyslug';
        $updateAgencyInput = new UpdateAgencyInput(
            'https://updated.com/here',
            'NR21 4SF',
            'SAMPLE'
        );

        $user = new User();
        $agency = (new Agency())->setSlug($slug);
        $differentAgency = (new Agency())->setSlug('different')->addAdminUser($user);

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->expectException(ForbiddenException::class);
        $this->entityManager->flush()->shouldNotBeCalled();

        $this->agencyService->updateAgency($slug, $updateAgencyInput, $user);
    }
}
