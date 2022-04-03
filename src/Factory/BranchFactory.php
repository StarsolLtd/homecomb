<?php

namespace App\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Model\Branch\Agency as AgencyModel;
use App\Model\Branch\Branch as BranchModel;
use App\Model\Branch\CreateInputInterface;
use App\Model\Branch\View;
use App\Util\BranchHelper;

class BranchFactory
{
    public function __construct(
        private BranchHelper $branchHelper,
        private TenancyReviewFactory $tenancyReviewFactory,
    ) {
    }

    public function createEntityFromCreateBranchInput(CreateInputInterface $input, Agency $agency): Branch
    {
        $branch = (new Branch())
            ->setAgency($agency)
            ->setName($input->getBranchName())
            ->setTelephone($input->getTelephone())
            ->setEmail($input->getEmail())
            ->setPublished(true)
        ;

        $branch->setSlug($this->branchHelper->generateSlug($branch));

        return $branch;
    }

    public function createViewFromEntity(Branch $branchEntity): View
    {
        $agency = null;
        $agencyEntity = $branchEntity->getAgency();
        if (null !== $agencyEntity) {
            $logoImage = $agencyEntity->getLogoImage();
            $agency = new AgencyModel(
                $agencyEntity->getSlug(),
                $agencyEntity->getName(),
                $logoImage ? $logoImage->getImage() : null
            );
        }

        $branch = new BranchModel(
            $branchEntity->getSlug(),
            $branchEntity->getName(),
            $branchEntity->getTelephone(),
            $branchEntity->getEmail()
        );

        $tenancyReviews = [];
        foreach ($branchEntity->getPublishedTenancyReviews() as $tenancyReviewEntity) {
            $tenancyReviews[] = $this->tenancyReviewFactory->createViewFromEntity($tenancyReviewEntity);
        }

        return new View(
            $branch,
            $agency,
            $tenancyReviews
        );
    }
}
