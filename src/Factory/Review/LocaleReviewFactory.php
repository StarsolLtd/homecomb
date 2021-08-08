<?php

namespace App\Factory\Review;

use App\Entity\Locale\Locale;
use App\Entity\Review\LocaleReview;
use App\Entity\User;
use App\Model\Review\LocaleReviewView;
use App\Model\Review\SubmitLocaleReviewInput;
use App\Repository\TenancyReviewRepository;
use App\Util\ReviewHelper;

class LocaleReviewFactory
{
    private ReviewHelper $reviewHelper;
    private TenancyReviewRepository $tenancyReviewRepository;

    public function __construct(
        ReviewHelper $reviewHelper,
        TenancyReviewRepository $tenancyReviewRepository
    ) {
        $this->reviewHelper = $reviewHelper;
        $this->tenancyReviewRepository = $tenancyReviewRepository;
    }

    public function createEntity(
        SubmitLocaleReviewInput $input,
        Locale $locale,
        ?User $user = null
    ): LocaleReview {
        $localeReview = (new LocaleReview())
            ->setLocale($locale)
            ->setUser($user)
            ->setTitle($input->getReviewTitle())
            ->setContent($input->getReviewContent())
            ->setAuthor($input->getReviewerName())
            ->setOverallStars($input->getOverallStars());

        assert($localeReview instanceof LocaleReview);

        $tenancyReviewSlug = $input->getTenancyReviewSlug();
        if (null !== $tenancyReviewSlug && '' !== $tenancyReviewSlug) {
            $tenancyReview = $this->tenancyReviewRepository->findOneNullableBySlug($tenancyReviewSlug);
            $localeReview->setTenancyReview($tenancyReview);
        }

        $localeReview->setSlug($this->reviewHelper->generateSlug($localeReview));

        return $localeReview;
    }

    public function createViewFromEntity(LocaleReview $review): LocaleReviewView
    {
        return new LocaleReviewView(
            $review->getId(),
            $review->getSlug(),
            $review->getAuthor(),
            $review->getTitle(),
            $review->getContent(),
            $review->getOverallStars(),
            $review->getCreatedAt(),
            $review->getPositiveVotesCount(),
            $review->getNegativeVotesCount(),
            $review->getVotesScore(),
        );
    }
}
