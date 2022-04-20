<?php

namespace App\Util;

use App\Entity\BroadbandProviderReview;

class BroadbandProviderReviewHelper
{
    public function generateSlug(BroadbandProviderReview $review): string
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
