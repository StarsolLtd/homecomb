<?php

namespace App\Entity\Comment;

use App\Entity\User;
use App\Entity\Vote\CommentVote;
use App\Entity\VoteableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="related_entity_name", type="string")
 * @ORM\DiscriminatorMap({
 *     "Review" = "ReviewComment"
 * })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
abstract class Comment
{
    use SoftDeleteableEntity;
    use TimestampableEntity;
    use VoteableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private User $user;

    /**
     * @ORM\Column(type="integer")
     */
    private int $relatedEntityId;

    /**
     * @ORM\Column(type="text", length=65535, nullable=false)
     */
    private string $content;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private bool $published = false;

    /**
     * @var Collection<int, CommentVote>
     * @ORM\OneToMany(targetEntity="App\Entity\Vote\CommentVote", mappedBy="comment")
     */
    private Collection $votes;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $User): self
    {
        $this->user = $User;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getRelatedEntityId(): int
    {
        return $this->relatedEntityId;
    }

    public function setRelatedEntityId(int $relatedEntityId): self
    {
        $this->relatedEntityId = $relatedEntityId;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return Collection<int, CommentVote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(CommentVote $vote): self
    {
        if ($this->votes->contains($vote)) {
            return $this;
        }
        $this->votes[] = $vote;
        $vote->setComment($this);

        return $this;
    }
}
