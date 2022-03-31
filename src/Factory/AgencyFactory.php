<?php

namespace App\Factory;

use App\Entity\Agency;
use App\Model\Agency\AgencyView;
use App\Model\Agency\CreateInputInterface;
use App\Util\AgencyHelper;

class AgencyFactory
{
    public function __construct(
        private AgencyHelper $agencyHelper,
        private FlatModelFactory $flatModelFactory,
    ) {
    }

    public function createAgencyEntityFromCreateAgencyInputModel(CreateInputInterface $createInput): Agency
    {
        $agency = (new Agency())
            ->setName($createInput->getAgencyName())
            ->setPostcode($createInput->getPostcode())
            ->setExternalUrl($createInput->getExternalUrl());

        $agency->setSlug($this->agencyHelper->generateSlug($agency));

        return $agency;
    }

    public function createEntityByName(string $name): Agency
    {
        $agency = (new Agency())
            ->setName($name);

        $agency->setSlug($this->agencyHelper->generateSlug($agency));

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
