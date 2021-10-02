<?php

namespace App\Service;

use App\Factory\Review\LocaleReviewFactory;
use App\Model\Review\SubmitLocaleReviewInput;
use App\Model\Review\SubmitLocaleReviewOutput;
use App\Repository\Locale\LocaleRepository;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ReviewService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotificationService $notificationService,
        private LocaleReviewFactory $localeReviewFactory,
        private LocaleRepository $localeRepository,
        private UserService $userService
    ) {
    }

    public function submitLocaleReview(SubmitLocaleReviewInput $submitInput, ?UserInterface $user): SubmitLocaleReviewOutput
    {
        $userEntity = $this->userService->getUserEntityOrNullFromUserInterface($user);

        $locale = $this->localeRepository->findOnePublishedBySlug($submitInput->getLocaleSlug());

        $localeReview = $this->localeReviewFactory->createEntity($submitInput, $locale, $userEntity);

        $this->entityManager->persist($localeReview);
        $this->entityManager->flush();

        $this->notificationService->sendLocaleReviewModerationNotification($localeReview);

        return new SubmitLocaleReviewOutput(true);
    }
}
