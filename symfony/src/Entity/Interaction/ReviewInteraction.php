<?php

namespace App\Entity\Interaction;

use App\Entity\Review;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ReviewInteraction extends Interaction
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Review")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private Review $review;

    public function getReview(): Review
    {
        return $this->review;
    }

    public function setReview(Review $review): self
    {
        $this->review = $review;

        return $this;
    }
}
