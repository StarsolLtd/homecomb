<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReviewRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Review
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="Branch", inversedBy="reviews")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id", nullable=true)
     */
    private ?Branch $branch = null;

    /**
     * @ORM\ManyToOne(targetEntity="Property", inversedBy="reviews")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id", nullable=false)
     */
    private Property $property;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reviews")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private ?User $user = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $content;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private ?int $overallStars;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private ?int $propertyStars;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private ?int $agencyStars;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private ?int $landlordStars;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private bool $published = false;

    /**
     * @var Collection<int, Locale>
     * @ORM\ManyToMany(targetEntity="Locale", mappedBy="reviews")
     * @ORM\JoinTable(name="locale_review")
     */
    private Collection $locales;

    /**
     * @var Collection<int, Image>
     * @ORM\OneToMany(targetEntity="Image", mappedBy="review")
     */
    private Collection $images;

    public function __construct()
    {
        $this->locales = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function __toString(): string
    {
        return 'Review '.$this->getId().' by '.$this->getAuthor();
    }

    /**
     * Use only for testing.
     */
    public function setIdForTest(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
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

        return $this;
    }
}
