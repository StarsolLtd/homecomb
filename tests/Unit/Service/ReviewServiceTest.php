<?php

namespace App\Tests\Unit\Service;

use App\Entity\Locale\Locale;
use App\Entity\Review\LocaleReview;
use App\Entity\User;
use App\Factory\Review\LocaleReviewFactory;
use App\Model\Review\SubmitLocaleReviewInput;
use App\Repository\Locale\LocaleRepository;
use App\Service\NotificationService;
use App\Service\ReviewService;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\ReviewService
 */
final class ReviewServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private ReviewService $reviewService;

    private ObjectProphecy $notificationService;
    private ObjectProphecy $localeReviewFactory;
    private ObjectProphecy $localeRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->notificationService = $this->prophesize(NotificationService::class);
        $this->localeReviewFactory = $this->prophesize(LocaleReviewFactory::class);
        $this->localeRepository = $this->prophesize(LocaleRepository::class);
        $this->userService = $this->prophesize(UserService::class);

        $this->reviewService = new ReviewService(
            $this->entityManager->reveal(),
            $this->notificationService->reveal(),
            $this->localeReviewFactory->reveal(),
            $this->localeRepository->reveal(),
            $this->userService->reveal(),
        );
    }

    /**
     * @covers \App\Service\ReviewService::submitLocaleReview
     */
    public function testSubmitReview1(): void
    {
        $submitInput = $this->prophesize(SubmitLocaleReviewInput::class);
        $localeReview = $this->prophesize(LocaleReview::class);
        $locale = $this->prophesize(Locale::class);
        $user = new User();

        $this->assertGetUserEntityOrNullFromInterface($user);

        $submitInput->getLocaleSlug()
            ->shouldBeCalledOnce()
            ->willReturn('test-slug');

        $this->localeRepository->findOnePublishedBySlug('test-slug')
            ->shouldBeCalledOnce()
            ->willReturn($locale);

        $this->localeReviewFactory->createEntity($submitInput, $locale, $user)
            ->shouldBeCalledOnce()
            ->willReturn($localeReview);

        $this->assertEntitiesArePersistedAndFlush([$localeReview]);

        $this->notificationService->sendLocaleReviewModerationNotification($localeReview)->shouldBeCalledOnce();

        $output = $this->reviewService->submitLocaleReview($submitInput->reveal(), $user);

        $this->assertTrue($output->isSuccess());
    }
}
