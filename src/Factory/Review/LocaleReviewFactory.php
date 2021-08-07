<?php

namespace App\Factory\Review;

use App\Entity\Locale\Locale;
use App\Entity\Review\LocaleReview;
use App\Entity\User;
use App\Model\Review\SubmitLocaleReviewInput;
use App\Util\ReviewHelper;

class LocaleReviewFactory
{
    private ReviewHelper $reviewHelper;

    public function __construct(
        ReviewHelper $reviewHelper
    ) {
        $this->reviewHelper = $reviewHelper;
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

        $localeReview->setSlug($this->reviewHelper->generateSlug($localeReview));

        assert($localeReview instanceof LocaleReview);

        return $localeReview;
    }
}
