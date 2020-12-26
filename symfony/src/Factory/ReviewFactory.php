<?php

namespace App\Factory;

use App\Entity\Review;
use App\Model\Review\View;

class ReviewFactory
{
    private AgencyFactory $agencyFactory;
    private BranchFactory $branchFactory;
    private PropertyFactory $propertyFactory;

    public function __construct(
        AgencyFactory $agencyFactory,
        BranchFactory $branchFactory,
        PropertyFactory $propertyFactory
    ) {
        $this->agencyFactory = $agencyFactory;
        $this->branchFactory = $branchFactory;
        $this->propertyFactory = $propertyFactory;
    }

    public function createViewFromEntity(Review $entity): View
    {
        $agency = null;
        $agencyEntity = $entity->getAgency();
        if (null !== $agencyEntity) {
            $agency = $this->agencyFactory->createFlatModelFromEntity($agencyEntity);
        }

        $branch = null;
        $branchEntity = $entity->getbranch();
        if (null !== $branchEntity) {
            $branch = $this->branchFactory->createFlatModelFromEntity($branchEntity);
        }

        $property = null;
        $propertyEntity = $entity->getproperty();
        if (null !== $propertyEntity) {
            $property = $this->propertyFactory->createFlatModelFromEntity($propertyEntity);
        }

        return new View(
            $branch,
            $agency,
            $property,
            $entity->getId() ?? 0,
            $entity->getAuthor() ?? '',
            $entity->getTitle() ?? '',
            $entity->getContent() ?? ''
        );
    }
}
