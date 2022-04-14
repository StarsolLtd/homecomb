<?php

namespace App\Repository;

use App\Entity\BroadbandProvider;

interface BroadbandProviderRepositoryInterface
{
    /**
     * @return ?BroadbandProvider
     */
    public function findOneBy(array $criteria, array $orderBy = null);

    public function findOnePublishedBySlug(string $slug): BroadbandProvider;
}
