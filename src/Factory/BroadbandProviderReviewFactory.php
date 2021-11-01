<?php

namespace App\Factory;

use App\Entity\BroadbandProvider;
use App\Entity\BroadbandProviderReview;
use App\Entity\User;
use App\Model\BroadbandProviderReview\SubmitInput;
use App\Util\BroadbandProviderReviewHelper;

class BroadbandProviderReviewFactory
{
    public function __construct(
        private BroadbandProviderReviewHelper $broadbandProviderReviewHelper
    ) {
    }

    public function createEntity(
        SubmitInput $input,
        BroadbandProvider $broadbandProvider,
        ?User $user
    ): BroadbandProviderReview {
        $review = (new BroadbandProviderReview())
            ->setBroadbandProvider($broadbandProvider)
            ->setUser($user)
            ->setAuthor($input->getReviewerName())
            ->setTitle($input->getReviewTitle())
            ->setContent($input->getReviewContent())
            ->setOverallStars($input->getOverallStars())
        ;

        $review->setSlug($this->broadbandProviderReviewHelper->generateSlug($review));

        return $review;
    }
}
