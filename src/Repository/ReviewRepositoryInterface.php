<?php

namespace App\Repository;

use App\Entity\Review\Review;

interface ReviewRepositoryInterface
{
    public function findOnePublishedById(int $id): Review;

    public function findOnePublishedBySlug(string $slug): Review;

    public function findLastPublished(): ?Review;
}
