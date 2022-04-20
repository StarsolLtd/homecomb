<?php

namespace App\Util;

use App\Entity\Branch;
use App\Exception\DeveloperException;

class BranchHelper
{
    public function generateSlug(Branch $branch): string
    {
        $branchName = $branch->getName();
        if ('' === $branchName) {
            throw new DeveloperException('Unable to generate a slug for a Branch without a name.');
        }

        $sourceString = $branchName;
        $agency = $branch->getAgency();
        if (null !== $agency) {
            $sourceString .= '/'.$agency->getName();
        }

        return substr(md5($sourceString), 0, 13);
    }
}
