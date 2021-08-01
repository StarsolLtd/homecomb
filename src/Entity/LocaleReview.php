<?php

namespace App\Entity;

use App\Entity\Vote\LocaleReviewVote;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocaleReviewRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class LocaleReview
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
     * @ORM\ManyToOne(targetEntity="Locale", inversedBy="LocaleReviews")
     * @ORM\JoinColumn(name="locale_id", referencedColumnName="id", nullable=false)
     */
    private Locale $locale;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="LocaleReviews")
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
     * @var Collection<int, Image>
     * @ORM\OneToMany(targetEntity="Image", mappedBy="LocaleReview")
     */
    private Collection $images;

    /**
     * @var Collection<int, LocaleReviewVote>
     * @ORM\OneToMany(targetEntity="App\Entity\Vote\LocaleReviewVote", mappedBy="LocaleReview")
     */
    private Collection $votes;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return 'LocaleReview '.$this->getId().' by '.$this->getAuthor();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function setLocale(Locale $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $User): self
    {
        $this->user = $User;

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

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            return $this;
        }
        $this->images[] = $image;
        $image->setLocaleReview($this);

        return $this;
    }

    /**
     * @return Collection<int, LocaleReviewVote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(LocaleReviewVote $vote): self
    {
        if ($this->votes->contains($vote)) {
            return $this;
        }
        $this->votes[] = $vote;
        $vote->setLocaleReview($this);

        return $this;
    }
}
