<?php

namespace App\Repository\Locale;

use App\Entity\Locale\Locale;
use Doctrine\Common\Collections\ArrayCollection;

interface LocaleRepositoryInterface
{
    public function findOnePublishedBySlug(string $slug): Locale;

    /**
     * @return ArrayCollection<int, Locale>
     */
    public function findBySearchQuery(string $searchQuery, int $maxResults = 10): ArrayCollection;
}
