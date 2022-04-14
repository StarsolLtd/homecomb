<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\TenancyReview;

interface TenancyReviewRepositoryInterface
{
    /**
     * @param mixed $id
     * @param mixed $lockMode
     * @param mixed $lockVersion
     *
     * @return TenancyReview|null
     */
    public function find($id, $lockMode = null, $lockVersion = null);

    public function findLastByPropertyAndAuthorOrNull(Property $property, string $author): ?TenancyReview;

    public function findOneById(int $id): TenancyReview;

    public function findOnePublishedById(int $id): TenancyReview;

    public function findLastPublished(): ?TenancyReview;

    /**
     * @return TenancyReview[]
     */
    public function findLatest(int $limit = 3): array;
}
