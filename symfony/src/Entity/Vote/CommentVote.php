<?php

namespace App\Entity\Vote;

use App\Entity\Comment\Comment;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CommentVote extends Vote
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Comment\Comment")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private Comment $Comment;

    public function getComment(): Comment
    {
        return $this->Comment;
    }

    public function setComment(Comment $Comment): self
    {
        $this->Comment = $Comment;

        return $this;
    }
}
