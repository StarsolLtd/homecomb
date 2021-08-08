<?php

namespace App\Util;

use App\Entity\TenancyReview;

class TenancyReviewHelper
{
    public function generateSlug(TenancyReview $tenancyReview): string
    {
        $user = $tenancyReview->getUser();

        $fields = implode('_', [
            $tenancyReview->getProperty()->getId(),
            $tenancyReview->getAuthor(),
            $tenancyReview->getTitle(),
            $tenancyReview->getContent(),
            null !== $user ? $user->getId() : '0',
        ]);

        return substr(md5($fields), 0, 15);
    }
}
