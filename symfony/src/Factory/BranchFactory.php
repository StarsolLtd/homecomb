<?php

namespace App\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Model\Branch\Agency as AgencyModel;
use App\Model\Branch\Branch as BranchModel;
use App\Model\Branch\CreateBranchInput;
use App\Model\Branch\View;
use App\Util\BranchHelper;

class BranchFactory
{
    private BranchHelper $branchHelper;
    private ReviewFactory $reviewFactory;

    public function __construct(
        BranchHelper $branchHelper,
        ReviewFactory $reviewFactory
    ) {
        $this->branchHelper = $branchHelper;
        $this->reviewFactory = $reviewFactory;
    }

    public function createBranchEntityFromCreateBranchInputModel(CreateBranchInput $createBranchInput, Agency $agency): Branch
    {
        $branch = (new Branch())
            ->setAgency($agency)
            ->setName($createBranchInput->getBranchName())
            ->setTelephone($createBranchInput->getTelephone())
            ->setEmail($createBranchInput->getEmail())
        ;

        $this->branchHelper->generateSlug($branch);

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

        $reviews = [];
        foreach ($branchEntity->getReviews() as $reviewEntity) {
            $reviews[] = $this->reviewFactory->createViewFromEntity($reviewEntity);
        }

        return new View(
            $branch,
            $agency,
            $reviews
        );
    }
}
