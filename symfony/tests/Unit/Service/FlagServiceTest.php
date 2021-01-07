<?php

namespace App\Tests\Unit\Service;

use App\Entity\Flag\ReviewFlag;
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

    public function testSubmitFlagIsSuccessWithValidData(): void
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

    public function testSubmitFlagThrowsExceptionWithInvalidEntityName(): void
    {
        $input = new SubmitInput('Chopsticks', 789, 'These are utensils for eating food');

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Chopsticks is not a valid flag entity name.');

        $this->flagService->submitFlag($input, null);
    }

    public function testSubmitFlagIsSuccessWithUserAndValidData(): void
    {
        $input = new SubmitInput('Review', 789, 'This is spam');
        $review = $this->prophesize(Review::class);
        $user = (new User())->setEmail('jack@starsol.co.uk');

        $this->reviewRepository->findOnePublishedById(789)
            ->shouldBeCalledOnce()
            ->willReturn($review);

        $this->entityManagerMock->persist(Argument::type(ReviewFlag::class))->shouldBeCalledOnce();
        $this->entityManagerMock->flush()->shouldBeCalledOnce();

        $this->userServiceMock->getUserEntityOrNullFromUserInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->notificationServiceMock->sendFlagModerationNotification(Argument::type(ReviewFlag::class))->shouldBeCalledOnce();

        $output = $this->flagService->submitFlag($input, $user);

        $this->assertTrue($output->isSuccess());
    }
}
