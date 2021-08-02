<?php

namespace App\Util;

use App\Entity\Review\Review;
use function md5;
use function substr;

class ReviewHelper
{
    public function generateSlug(Review $review): string
    {
        $user = $review->getUser();

        $fields = implode('_', [
            $review->getRelatedEntityId(),
            $review->getAuthor(),
            $review->getTitle(),
            null !== $user ? $user->getId() : '0',
        ]);

        return substr(md5($fields), 0, 15);
    }
}
