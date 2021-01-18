<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Flag\AgencyFlag;
use App\Entity\Flag\BranchFlag;
use App\Entity\Flag\PropertyFlag;
use App\Entity\Flag\ReviewFlag;
use App\Entity\Property;
use App\Entity\Review;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Model\Flag\SubmitInput;
use App\Repository\AgencyRepository;
use App\Repository\BranchRepository;
use App\Repository\PropertyRepository;
use App\Repository\ReviewRepository;
use App\Service\FlagService;
use App\Service\NotificationService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Service\FlagService
 */
class FlagServiceTest extends TestCase
{
    use ProphecyTrait;

    private FlagService $flagService;

    private $entityManagerMock;
    private $notificationServiceMock;
    private $userServiceMock;
    private $agencyRepository;
    private $branchRepository;
    private $propertyRepository;
    private $reviewRepository;

    public function setUp(): void
    {
        $this->entityManagerMock = $this->prophesize(EntityManagerInterface::class);
        $this->notificationServiceMock = $this->prophesize(NotificationService::class);
        $this->userServiceMock = $this->prophesize(UserService::class);
        $this->agencyRepository = $this->prophesize(AgencyRepository::class);
        $this->branchRepository = $this->prophesize(BranchRepository::class);
        $this->propertyRepository = $this->prophesize(PropertyRepository::class);
        $this->reviewRepository = $this->prophesize(ReviewRepository::class);

        $this->flagService = new FlagService(
            $this->entityManagerMock->reveal(),
            $this->notificationServiceMock->reveal(),
            $this->userServiceMock->reveal(),
            $this->agencyRepository->reveal(),
            $this->branchRepository->reveal(),
            $this->propertyRepository->reveal(),
            $this->reviewRepository->reveal(),
        );
    }

    /**
     * @covers \App\Service\FlagService::submitFlag
     * Test success with valid review data.
     */
    public function testSubmitFlag1(): void
    {
        $input = new SubmitInput('Review', 789, 'This is spam');
        $review = $this->prophesize(Review::class);

        $this->reviewRepository->findOnePublishedById(789)
            ->shouldBeCalledOnce()
            ->willReturn($review);

        $this->entityManagerMock->persist(Argument::type(ReviewFlag::class))->shouldBeCalledOnce();
        $this->entityManagerMock->flush()->shouldBeCalledOnce();

        $this->notificationServiceMock->sendFlagModerationNotification(Argument::type(ReviewFlag::class))->shouldBeCalledOnce();

        $output = $this->flagService->submitFlag($input, null);

        $this->assertTrue($output->isSuccess());
    }

    /**
     * @covers \App\Service\FlagService::submitFlag
     * Test throws exception with invalid entity name
     */
    public function testSubmitFlag2(): void
    {
        $input = new SubmitInput('Chopsticks', 789, 'These are utensils for eating food');

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Chopsticks is not a valid flag entity name.');

        $this->flagService->submitFlag($input, null);
    }

    /**
     * @covers \App\Service\FlagService::submitFlag
     * Test success with valid agency data and logged in user
     */
    public function testSubmitFlag3(): void
    {
        $input = new SubmitInput('Agency', 22, 'Not a real company');
        $agency = $this->prophesize(Agency::class);
        $user = $this->prophesize(User::class);

        $this->agencyRepository->findOnePublishedById(22)
            ->shouldBeCalledOnce()
            ->willReturn($agency);

        $this->entityManagerMock->persist(Argument::type(AgencyFlag::class))->shouldBeCalledOnce();
        $this->entityManagerMock->flush()->shouldBeCalledOnce();

        $this->userServiceMock->getUserEntityOrNullFromUserInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->notificationServiceMock->sendFlagModerationNotification(Argument::type(AgencyFlag::class))->shouldBeCalledOnce();

        $output = $this->flagService->submitFlag($input, $user->reveal());

        $this->assertTrue($output->isSuccess());
    }

    /**
     * @covers \App\Service\FlagService::submitFlag
     * Test success with valid branch data.
     */
    public function testSubmitFlag4(): void
    {
        $input = new SubmitInput('Branch', 999, 'The agency does not have a branch here');
        $branch = $this->prophesize(Branch::class);

        $this->branchRepository->findOnePublishedById(999)
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $this->entityManagerMock->persist(Argument::type(BranchFlag::class))->shouldBeCalledOnce();
        $this->entityManagerMock->flush()->shouldBeCalledOnce();

        $this->notificationServiceMock->sendFlagModerationNotification(Argument::type(BranchFlag::class))->shouldBeCalledOnce();

        $output = $this->flagService->submitFlag($input, null);

        $this->assertTrue($output->isSuccess());
    }

    /**
     * @covers \App\Service\FlagService::submitFlag
     * Test success with valid property data.
     */
    public function testSubmitFlag5(): void
    {
        $input = new SubmitInput('Property', 2021, 'This property is not real');
        $property = $this->prophesize(Property::class);

        $this->propertyRepository->findOnePublishedById(2021)
            ->shouldBeCalledOnce()
            ->willReturn($property);

        $this->entityManagerMock->persist(Argument::type(PropertyFlag::class))->shouldBeCalledOnce();
        $this->entityManagerMock->flush()->shouldBeCalledOnce();

        $this->notificationServiceMock->sendFlagModerationNotification(Argument::type(PropertyFlag::class))->shouldBeCalledOnce();

        $output = $this->flagService->submitFlag($input, null);

        $this->assertTrue($output->isSuccess());
    }
}
