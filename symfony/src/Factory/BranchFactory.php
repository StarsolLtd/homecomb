<?php

namespace App\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Model\Branch\CreateBranchInput;
use App\Util\BranchHelper;

class BranchFactory
{
    private BranchHelper $branchHelper;

    public function __construct(
        BranchHelper $branchHelper
    ) {
        $this->branchHelper = $branchHelper;
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
}
