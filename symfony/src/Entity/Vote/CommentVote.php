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
    private Comment $comment;

    public function getComment(): Comment
    {
        return $this->comment;
    }

    public function setComment(Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
