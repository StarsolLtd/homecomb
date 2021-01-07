<?php

namespace App\Entity\Flag;

use App\Entity\Branch;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class BranchFlag extends Flag
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Branch")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private Branch $branch;

    public function getBranch(): Branch
    {
        return $this->branch;
    }

    public function setBranch(Branch $branch): self
    {
        $this->branch = $branch;

        return $this;
    }
}
