<?php

namespace App\Factory;

use App\Entity\Review;
use App\Model\Review\Stars;
use App\Model\Review\View;

class ReviewFactory
{
    private FlatModelFactory $flatModelFactory;

    public function __construct(
        FlatModelFactory $flatModelFactory
    ) {
        $this->flatModelFactory = $flatModelFactory;
    }

    public function createViewFromEntity(Review $entity): View
    {
        $agency = null;
        $agencyEntity = $entity->getAgency();
        if (null !== $agencyEntity) {
            $agency = $this->flatModelFactory->getAgencyFlatModel($agencyEntity);
        }

        $branch = null;
        $branchEntity = $entity->getbranch();
        if (null !== $branchEntity) {
            $branch = $this->flatModelFactory->getBranchFlatModel($branchEntity);
        }

        $property = null;
        $propertyEntity = $entity->getproperty();
        if (null !== $propertyEntity) {
            $property = $this->flatModelFactory->getPropertyFlatModel($propertyEntity);
        }

        $stars = $this->createStarsFromEntity($entity);

        return new View(
            $branch,
            $agency,
            $property,
            $entity->getId() ?? 0,
            $entity->getAuthor() ?? '',
            $entity->getTitle() ?? '',
            $entity->getContent() ?? '',
            $stars,
            $entity->getCreatedAt()
        );
    }

    private function createStarsFromEntity(Review $entity): Stars
    {
        return new Stars(
            $entity->getOverallStars(),
            $entity->getPropertyStars(),
            $entity->getAgencyStars(),
            $entity->getLandlordStars(),
        );
    }
}
