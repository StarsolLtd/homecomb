<?php

namespace App\Entity\Review;

use App\Entity\User;
use App\Entity\Vote\Vote;
use App\Entity\VoteableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReviewRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="related_entity_name", type="string")
 * @ORM\DiscriminatorMap({
 *     "Locale" = "LocaleReview"
 * })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
abstract class Review
{
    use SoftDeleteableEntity;
    use TimestampableEntity;
    use VoteableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reviews")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private ?User $user = null;

    /**
     * @ORM\Column(type="integer")
     */
    private int $relatedEntityId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $author = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private ?int $overallStars = null;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private bool $published = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private string $slug;

    protected Collection $votes;

    abstract public function addVote(Vote $vote): self;

    /**
     * @return Collection<int, Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function __construct()
    {
        $this->votes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getOverallStars(): ?int
    {
        return $this->overallStars;
    }

    public function setOverallStars(?int $overallStars): self
    {
        $this->overallStars = $overallStars;

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
}
