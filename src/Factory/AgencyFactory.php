<?php

namespace App\Factory;

use App\Entity\Agency;
use App\Model\Agency\AgencyView;
use App\Model\Agency\CreateAgencyInput;
use App\Util\AgencyHelper;

class AgencyFactory
{
    private AgencyHelper $agencyHelper;
    private FlatModelFactory $flatModelFactory;

    public function __construct(
        AgencyHelper $agencyHelper,
        FlatModelFactory $flatModelFactory
    ) {
        $this->agencyHelper = $agencyHelper;
        $this->flatModelFactory = $flatModelFactory;
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
            $branches[] = $this->flatModelFactory->getBranchFlatModel($branch);
        }

        return new AgencyView(
            $agency->getSlug() ?? '',
            $agency->getName() ?? '',
            $branches
        );
    }
}
