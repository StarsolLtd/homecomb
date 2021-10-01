<?php

namespace App\Entity\Interaction;

use App\Entity\Vote\Vote;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class VoteInteraction extends Interaction
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Vote\Vote")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private Vote $Vote;

    public function getVote(): Vote
    {
        return $this->Vote;
    }

    public function setVote(Vote $vote): self
    {
        $this->Vote = $vote;

        return $this;
    }
}
