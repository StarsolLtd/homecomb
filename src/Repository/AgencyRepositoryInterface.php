<?php

namespace App\Repository;

use App\Entity\Agency;

interface AgencyRepositoryInterface
{
    /**
     * @return ?Agency
     */
    public function findOneBy(array $criteria, array $orderBy = null);

    public function findOnePublishedBySlug(string $slug): Agency;

    public function findOneBySlugOrNull(string $slug): ?Agency;

    public function findOnePublishedById(int $id): Agency;
}
