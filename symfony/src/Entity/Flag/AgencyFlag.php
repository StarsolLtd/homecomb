<?php

namespace App\Entity\Flag;

use App\Entity\Agency;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class AgencyFlag extends Flag
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Agency")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private Agency $agency;

    public function getAgency(): Agency
    {
        return $this->agency;
    }

    public function setAgency(Agency $agency): self
    {
        $this->agency = $agency;

        return $this;
    }
}
