<?php

namespace App\Entity\Comment;

use App\Entity\Review;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ReviewComment extends Comment
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Review")
     * @ORM\JoinColumn(name="related_entity_id", referencedColumnName="id")
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
