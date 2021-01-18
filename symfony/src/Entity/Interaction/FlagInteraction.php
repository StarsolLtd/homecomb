<?php

namespace App\Entity\Interaction;

use App\Entity\Flag\Flag;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class FlagInteraction extends Interaction
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Flag\Flag")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private Flag $flag;

    public function getFlag(): Flag
    {
        return $this->flag;
    }

    public function setFlag(Flag $flag): self
    {
        $this->flag = $flag;

        return $this;
    }
}
