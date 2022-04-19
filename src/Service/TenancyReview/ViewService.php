<?php

namespace App\Service\TenancyReview;

use App\Factory\TenancyReviewFactory;
use App\Model\TenancyReview\Group;
use App\Model\TenancyReview\View;
use App\Repository\TenancyReviewRepositoryInterface;

class ViewService
{
    public function __construct(
        private TenancyReviewRepositoryInterface $tenancyReviewRepository,
        private TenancyReviewFactory $tenancyReviewFactory,
    ) {
    }

    public function getViewById(int $tenancyReviewId): View
    {
        $entity = $this->tenancyReviewRepository->findOnePublishedById($tenancyReviewId);

        return $this->tenancyReviewFactory->createViewFromEntity($entity);
    }

    public function getLatestGroup(int $limit = 3): Group
    {
        $tenancyReviews = $this->tenancyReviewRepository->findLatest($limit);

        return $this->tenancyReviewFactory->createGroup('Latest Reviews', $tenancyReviews);
    }
}
