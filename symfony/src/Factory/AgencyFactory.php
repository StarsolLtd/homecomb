<?php

namespace App\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Model\Agency\AgencyBranch;
use App\Model\Agency\AgencyView;
use App\Model\Agency\CreateAgencyInput;
use App\Util\AgencyHelper;

class AgencyFactory
{
    private AgencyHelper $agencyHelper;

    public function __construct(
        AgencyHelper $agencyHelper
    ) {
        $this->agencyHelper = $agencyHelper;
    }

    public function createAgencyEntityFromCreateAgencyInputModel(CreateAgencyInput $createAgencyInput): Agency
    {
        $agency = (new Agency())
            ->setName($createAgencyInput->getAgencyName())
            ->setPostcode($createAgencyInput->getPostcode())
            ->setExternalUrl($createAgencyInput->getExternalUrl());

        $this->agencyHelper->generateSlug($agency);

        return $agency;
    }

    public function createViewFromEntity(Agency $agency): AgencyView
    {
        $branches = [];
        foreach ($agency->getBranches() as $branch) {
            $branches[] = $this->createAgencyBranchFromBranchEntity($branch);
        }

        return new AgencyView(
            $agency->getSlug() ?? '',
            $agency->getName() ?? '',
            $branches
        );
    }

    private function createAgencyBranchFromBranchEntity(Branch $branch): AgencyBranch
    {
        return new AgencyBranch(
            $branch->getSlug() ?? '',
            $branch->getName() ?? '',
            $branch->getTelephone(),
            $branch->getEmail()
        );
    }
}
