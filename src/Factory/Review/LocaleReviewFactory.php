<?php

namespace App\Factory\Review;

use App\Entity\Locale\Locale;
use App\Entity\Review\LocaleReview;
use App\Entity\User;
use App\Model\Review\LocaleReviewView;
use App\Model\Review\SubmitLocaleReviewInputInterface;
use App\Util\ReviewHelper;

class LocaleReviewFactory
{
    public function __construct(
        private ReviewHelper $reviewHelper
    ) {
    }

    public function createEntity(
        SubmitLocaleReviewInputInterface $input,
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

        $localeReview->setSlug($this->reviewHelper->generateSlug($localeReview));

        assert($localeReview instanceof LocaleReview);

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
