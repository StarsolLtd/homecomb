<?php

namespace App\Util;

use App\Entity\Branch;
use LogicException;
use function md5;
use function substr;

class BranchHelper
{
    public function generateSlug(Branch $branch): string
    {
        if (null === $branch->getName()) {
            throw new LogicException('Unable to generate a slug for a Branch without a name.');
        }

        $sourceString = $branch->getName();
        $agency = $branch->getAgency();
        if (null !== $agency) {
            $sourceString .= '/'.$agency->getName();
        }

        $slug = substr(md5($sourceString), 0, 13);
        $branch->setSlug($slug);

        return $slug;
    }
}
