<?php

namespace App\Entity;

use App\Entity\Comment\TenancyReviewComment;
use App\Entity\Locale\Locale;
use App\Entity\Vote\TenancyReviewVote;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TenancyReviewRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class TenancyReview
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
     * @ORM\ManyToOne(targetEntity="Branch", inversedBy="tenancyReviews")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id", nullable=true)
     */
    private ?Branch $branch = null;

    /**
     * @ORM\ManyToOne(targetEntity="Property", inversedBy="tenancyReviews")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id", nullable=false)
     */
    private Property $property;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tenancyReviews")
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
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private ?int $propertyStars = null;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private ?int $agencyStars = null;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private ?int $landlordStars = null;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private bool $published = false;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $start;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $end;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private string $slug;

    /**
     * @var Collection<int, Locale>
     * @ORM\ManyToMany(targetEntity="App\Entity\Locale\Locale", mappedBy="tenancyReviews")
     * @ORM\JoinTable(name="locale_tenancy_review")
     */
    private Collection $locales;

    /**
     * @var Collection<int, Image>
     * @ORM\OneToMany(targetEntity="Image", mappedBy="tenancyReview")
     */
    private Collection $images;

    /**
     * @var Collection<int, TenancyReviewComment>
     * @ORM\OneToMany(targetEntity="App\Entity\Comment\TenancyReviewComment", mappedBy="tenancyReview")
     */
    private Collection $comments;

    /**
     * @var Collection<int, TenancyReviewVote>
     * @ORM\OneToMany(targetEntity="App\Entity\Vote\TenancyReviewVote", mappedBy="tenancyReview")
     */
    private Collection $votes;

    public function __construct()
    {
        $this->locales = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return 'TenancyReview '.$this->getId().' by '.$this->getAuthor();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAgency(): ?Agency
    {
        $branch = $this->getBranch();
        if (null === $branch) {
            return null;
        }

        return $branch->getAgency();
    }

    public function getBranch(): ?Branch
    {
        return $this->branch;
    }

    public function setBranch(?Branch $branch): self
    {
        $this->branch = $branch;

        return $this;
    }

    public function getProperty(): Property
    {
        return $this->property;
    }

    public function setProperty(Property $property): self
    {
        $this->property = $property;

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

    public function getPropertyStars(): ?int
    {
        return $this->propertyStars;
    }

    public function setPropertyStars(?int $propertyStars): self
    {
        $this->propertyStars = $propertyStars;

        return $this;
    }

    public function getAgencyStars(): ?int
    {
        return $this->agencyStars;
    }

    public function setAgencyStars(?int $agencyStars): self
    {
        $this->agencyStars = $agencyStars;

        return $this;
    }

    public function getLandlordStars(): ?int
    {
        return $this->landlordStars;
    }

    public function setLandlordStars(?int $landlordStars): self
    {
        $this->landlordStars = $landlordStars;

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

    public function getStart(): ?DateTime
    {
        return $this->start;
    }

    public function setStart(?DateTime $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    public function setEnd(?DateTime $end): self
    {
        $this->end = $end;

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

    /**
     * @return Collection<int, Locale>
     */
    public function getLocales(): Collection
    {
        return $this->locales;
    }

    public function addLocale(Locale $locale): self
    {
        if ($this->locales->contains($locale)) {
            return $this;
        }
        $this->locales[] = $locale;

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
        $image->setTenancyReview($this);

        return $this;
    }

    /**
     * @return Collection<int, TenancyReviewComment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return Collection<int, TenancyReviewComment>
     */
    public function getPublishedComments(): Collection
    {
        return $this->getComments()->filter(function (TenancyReviewComment $comment) {
            return $comment->isPublished();
        });
    }

    public function addComment(TenancyReviewComment $comment): self
    {
        if ($this->comments->contains($comment)) {
            return $this;
        }
        $this->comments[] = $comment;
        $comment->setTenancyReview($this);

        return $this;
    }

    /**
     * @return Collection<int, TenancyReviewVote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(TenancyReviewVote $vote): self
    {
        if ($this->votes->contains($vote)) {
            return $this;
        }
        $this->votes[] = $vote;
        $vote->setTenancyReview($this);

        return $this;
    }
}
