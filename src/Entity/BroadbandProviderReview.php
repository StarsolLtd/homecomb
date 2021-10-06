<?php

namespace App\Entity;

use App\Entity\Vote\BroadbandProviderReviewVote;
use App\Entity\Vote\Vote;
use App\Exception\DeveloperException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BroadbandProviderRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class BroadbandProviderReview
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reviews")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private ?User $user = null;

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

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BroadbandProvider")
     * @ORM\JoinColumn(name="related_entity_id", referencedColumnName="id")
     */
    private BroadbandProvider $broadbandProvider;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Postcode")
     * @ORM\JoinColumn(name="postcode", referencedColumnName="id")
     */
    private Postcode $postcode;

    /**
     * @var Collection<int, Vote>
     * @ORM\OneToMany(targetEntity="App\Entity\Vote\BroadbandProviderReviewVote", mappedBy="broadbandProviderReview")
     */
    protected Collection $votes;

    public function getId(): int
    {
        return $this->id;
    }

    public function addVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            return $this;
        }
        if (!($vote instanceof BroadbandProviderReviewVote)) {
            throw new DeveloperException('Only BroadbandProviderReviewVotes can be added to a BroadbandProviderReview');
        }
        $this->votes->add($vote);
        $vote->setBroadbandProviderReview($this);

        return $this;
    }

    public function getBroadbandProvider(): BroadbandProvider
    {
        return $this->broadbandProvider;
    }

    public function setBroadbandProvider(BroadbandProvider $broadbandProvider): self
    {
        $this->broadbandProvider = $broadbandProvider;

        return $this;
    }

    public function getPostcode(): Postcode
    {
        return $this->postcode;
    }

    public function setPostcode(Postcode $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
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
}
