<?php

namespace App\Entity\Flag;

use App\Entity\Property;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PropertyFlag extends Flag
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Property")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private Property $property;

    public function getProperty(): Property
    {
        return $this->property;
    }

    public function setProperty(Property $property): self
    {
        $this->property = $property;

        return $this;
    }
}
