<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocaleRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Locale
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $name;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $content;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $slug;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private bool $published = true;

    /**
     * @var Collection<int, Postcode>
     * @ORM\ManyToMany(targetEntity="Postcode", inversedBy="locales", cascade={"persist"})
     * @ORM\JoinTable(name="locale_postcode")
     */
    private Collection $postcodes;

    /**
     * @var Collection<int, Review>
     * @ORM\ManyToMany(targetEntity="Review", inversedBy="locales", cascade={"persist"})
     * @ORM\JoinTable(name="locale_review")
     */
    private Collection $reviews;

    /**
     * @var Collection<int, Locale>
     * @ORM\ManyToMany(targetEntity="Locale", mappedBy="relatedLocales")
     */
    private Collection $localesRelating;

    /**
     * @var Collection<int, Locale>
     * @ORM\ManyToMany(targetEntity="Locale", inversedBy="locatedRelating")
     * @ORM\JoinTable(name="locale_related",
     *      joinColumns={@ORM\JoinColumn(name="locale_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="related_locale_id", referencedColumnName="id")}
     *      )
     */
    private Collection $relatedLocales;

    /**
     * @var Collection<int, Image>
     * @ORM\OneToMany(targetEntity="Image", mappedBy="locale")
     */
    private Collection $images;

    public function __construct()
    {
        $this->postcodes = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->localesRelating = new ArrayCollection();
        $this->relatedLocales = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getSlug(): ?string
    {
        return $this->slug;
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
     * @return Collection<int, Postcode>
     */
    public function getPostcodes(): Collection
    {
        return $this->postcodes;
    }

    public function addPostcode(Postcode $postcode): self
    {
        if ($this->postcodes->contains($postcode)) {
            return $this;
        }
        $postcode->addLocale($this);
        $this->postcodes[] = $postcode;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            return $this;
        }
        $review->addLocale($this);
        $this->reviews[] = $review;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getPublishedReviews(): Collection
    {
        return $this->getReviews()->filter(function (Review $review) {
            return $review->isPublished();
        });
    }

    /**
     * @return Collection<int, Review>
     */
    public function getPublishedReviewsWithPublishedAgency(): Collection
    {
        return $this->getReviews()->filter(function (Review $review) {
            return $review->isPublished() && null !== $review->getAgency() && $review->getAgency()->isPublished();
        });
    }

    /**
     * @return Collection<int, Locale>
     */
    public function getRelatedLocales(): Collection
    {
        return $this->relatedLocales;
    }

    public function addRelatedLocale(Locale $locale): self
    {
        if ($this->relatedLocales->contains($locale)) {
            return $this;
        }
        $this->relatedLocales[] = $locale;

        return $this;
    }

    /**
     * @param Locale[] $locales
     */
    public function addRelatedLocales(array $locales): self
    {
        foreach ($locales as $locale) {
            $this->addRelatedLocale($locale);
        }

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
