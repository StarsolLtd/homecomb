<?php

namespace App\Service;

use App\Factory\Review\LocaleReviewFactory;
use App\Model\Review\SubmitLocaleReviewInput;
use App\Model\Review\SubmitLocaleReviewOutput;
use App\Repository\Locale\LocaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ReviewService
{
    private EntityManagerInterface $entityManager;
    private LocaleReviewFactory $localeReviewFactory;
    private LocaleRepository $localeRepository;
    private UserService $userService;

    public function __construct(
        EntityManagerInterface $entityManager,
        LocaleReviewFactory $localeReviewFactory,
        LocaleRepository $localeRepository,
        UserService $userService
    ) {
        $this->entityManager = $entityManager;
        $this->localeReviewFactory = $localeReviewFactory;
        $this->localeRepository = $localeRepository;
        $this->userService = $userService;
    }

    public function submitLocaleReview(SubmitLocaleReviewInput $submitInput, ?UserInterface $user): SubmitLocaleReviewOutput
    {
        $userEntity = $this->userService->getUserEntityOrNullFromUserInterface($user);

        $locale = $this->localeRepository->findOnePublishedBySlug($submitInput->getLocaleSlug());

        $localeReview = $this->localeReviewFactory->createEntity($submitInput, $locale, $userEntity);

        $this->entityManager->persist($localeReview);
        $this->entityManager->flush();

        return new SubmitLocaleReviewOutput(true);
    }
}
