<?php

namespace App\Util;

use App\Entity\Review\LocaleReview;
use App\Entity\Review\Review;
use App\Entity\TenancyReview;

class ReviewHelper
{
    public function generateTenancyReviewSlug(TenancyReview $tenancyReview): string
    {
        $review = (new LocaleReview())
            ->setAuthor($tenancyReview->getAuthor())
            ->setTitle($tenancyReview->getTitle())
            ->setContent($tenancyReview->getContent())
            ->setUser($tenancyReview->getUser());

        return $this->generateSlug($review);
    }

    public function generateSlug(Review $review): string
    {
        $user = $review->getUser();

        $fields = implode('_', [
            $review->getAuthor(),
            $review->getTitle(),
            $review->getContent(),
            null !== $user ? $user->getId() : '0',
        ]);

        return substr(md5($fields), 0, 15);
    }
}
