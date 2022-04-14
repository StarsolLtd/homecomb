<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\Property;
use Doctrine\Common\Collections\ArrayCollection;

interface PropertyRepositoryInterface
{
    public function findOnePublishedBySlug(string $slug): Property;

    public function findOnePublishedById(int $id): Property;

    public function findOneBySlugOrNull(string $slug): ?Property;

    public function findOneByVendorPropertyIdOrNull(string $vendorPropertyId): ?Property;

    public function findOneByAddressOrNull(string $addressLine1, string $postcode): ?Property;

    /**
     * @return ArrayCollection<int, Property>
     */
    public function findBySearchQuery(string $searchQuery, int $maxResults = 10): ArrayCollection;

    /**
     * @return ArrayCollection<int, Property>
     */
    public function findPublishedByCity(City $city): ArrayCollection;
}
