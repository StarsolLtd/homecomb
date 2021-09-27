<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\User;
use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Factory\AgencyFactory;
use App\Factory\FlatModelFactory;
use App\Model\Agency\AgencyView;
use App\Model\Agency\CreateAgencyInput;
use App\Model\Agency\Flat;
use App\Model\Agency\UpdateAgencyInput;
use App\Repository\AgencyRepository;
use App\Service\AgencyService;
use App\Service\NotificationService;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Util\AgencyHelper;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\AgencyService
 */
final class AgencyServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private AgencyService $agencyService;

    private ObjectProphecy $notificationService;
    private ObjectProphecy $userService;
    private ObjectProphecy $agencyFactory;
    private ObjectProphecy $flatModelFactory;
    private ObjectProphecy $agencyHelper;
    private ObjectProphecy $agencyRepository;

    public function setUp(): void
    {
        $this->notificationService = $this->prophesize(NotificationService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->agencyFactory = $this->prophesize(AgencyFactory::class);
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);
        $this->agencyHelper = $this->prophesize(AgencyHelper::class);
        $this->agencyRepository = $this->prophesize(AgencyRepository::class);

        $this->agencyService = new AgencyService(
            $this->notificationService->reveal(),
            $this->userService->reveal(),
            $this->entityManager->reveal(),
            $this->agencyFactory->reveal(),
            $this->flatModelFactory->reveal(),
            $this->agencyHelper->reveal(),
            $this->agencyRepository->reveal()
        );
    }

    /**
     * @covers \App\Service\AgencyService::findOrCreateByName
     */
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

    /**
     * @covers \App\Service\AgencyService::findOrCreateByName
     */
    public function testFindOrCreateByNameWhenAlreadyExists(): void
    {
        $agencyName = 'Cornwall Homes';

        $agency = (new Agency())->setName($agencyName);

        $this->agencyRepository->findOneBy(['name' => $agencyName])->shouldBeCalledOnce()->willReturn($agency);

        $this->assertEntityManagerUnused();

        $result = $this->agencyService->findOrCreateByName($agencyName);

        $this->assertEquals($agencyName, $result->getName());
    }

    /**
     * @covers \App\Service\AgencyService::createAgency
     */
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

    /**
     * @covers \App\Service\AgencyService::createAgency
     */
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

        $this->agencyService->createAgency($createAgencyInput, $user);
    }

    /**
     * @covers \App\Service\AgencyService::getAgencyForUser
     */
    public function testGetAgencyForUser1(): void
    {
        $slug = 'testagencyslug';
        $user = new User();
        $agency = (new Agency())->setSlug($slug)->addAdminUser($user);
        $agencyModel = $this->prophesize(Flat::class);

        $this->userService->getEntityFromInterface($user)
            ->shouldBeCalledOnce()
            ->willReturn($user);

        $this->flatModelFactory->getAgencyFlatModel($agency)
            ->shouldBeCalledOnce()
            ->willReturn($agencyModel);

        $this->assertEntityManagerUnused();

        $this->agencyService->getAgencyForUser($user);
    }

    /**
     * @covers \App\Service\AgencyService::getAgencyForUser
     * Test throws NotFoundException when user is not an agency admin
     */
    public function testGetAgencyForUser2(): void
    {
        $user = new User();

        $this->userService->getEntityFromInterface($user)
            ->shouldBeCalledOnce()
            ->willReturn($user);

        $this->expectException(NotFoundException::class);

        $this->agencyService->getAgencyForUser($user);

        $this->assertEntityManagerUnused();
    }

    /**
     * @covers \App\Service\AgencyService::updateAgency
     */
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

    /**
     * @covers \App\Service\AgencyService::updateAgency
     */
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
        $this->assertEntityManagerUnused();

        $this->agencyService->updateAgency($slug, $updateAgencyInput, $user);
    }

    /**
     * @covers \App\Service\AgencyService::updateAgency
     */
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
        $this->assertEntityManagerUnused();

        $this->agencyService->updateAgency($slug, $updateAgencyInput, $user);
    }

    /**
     * @covers \App\Service\AgencyService::getViewBySlug
     */
    public function testGetViewBySlug1(): void
    {
        $agency = new Agency();
        $agencyView = $this->prophesize(AgencyView::class);

        $this->agencyRepository->findOnePublishedBySlug('agencyslug')
            ->shouldBeCalledOnce()
            ->willReturn($agency);

        $this->agencyFactory->createViewFromEntity($agency)
            ->shouldBeCalled()
            ->willReturn($agencyView);

        $output = $this->agencyService->getViewBySlug('agencyslug');

        $this->assertEquals($agencyView->reveal(), $output);

        $this->assertEntityManagerUnused();
    }
}
