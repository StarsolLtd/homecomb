<?php

namespace App\Factory;

use App\Entity\Agency;
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
}
