<?php

namespace App\Entity\Locale;

use App\Entity\Image;
use App\Entity\Postcode;
use App\Entity\TenancyReview;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocaleRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "City" = "CityLocale",
 *     "Locale" = "Locale",
 * })
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Postcode", inversedBy="locales", cascade={"persist"})
     * @ORM\JoinTable(name="locale_postcode")
     */
    private Collection $postcodes;

    /**
     * @var Collection<int, TenancyReview>
     * @ORM\ManyToMany(targetEntity="App\Entity\TenancyReview", inversedBy="locales", cascade={"persist"})
     * @ORM\JoinTable(name="locale_tenancy_review")
     */
    private Collection $tenancyReviews;

    /**
     * @var Collection<int, Locale>
     * @ORM\ManyToMany(targetEntity="App\Entity\Locale\Locale", mappedBy="relatedLocales")
     */
    private Collection $localesRelating;

    /**
     * @var Collection<int, Locale>
     * @ORM\ManyToMany(targetEntity="App\Entity\Locale\Locale", inversedBy="locatedRelating")
     * @ORM\JoinTable(name="locale_related",
     *      joinColumns={@ORM\JoinColumn(name="locale_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="related_locale_id", referencedColumnName="id")}
     *      )
     */
    private Collection $relatedLocales;

    /**
     * @var Collection<int, Image>
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="locale")
     */
    private Collection $images;

    public function __construct()
    {
        $this->postcodes = new ArrayCollection();
        $this->tenancyReviews = new ArrayCollection();
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

    public function getName(): string
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlugForTest(string $slug): self
    {
        $this->slug = $slug;

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
     * @return Collection<int, TenancyReview>
     */
    public function getTenancyReviews(): Collection
    {
        return $this->tenancyReviews;
    }

    public function addTenancyReview(TenancyReview $tenancyReview): self
    {
        if ($this->tenancyReviews->contains($tenancyReview)) {
            return $this;
        }
        $tenancyReview->addLocale($this);
        $this->tenancyReviews[] = $tenancyReview;

        return $this;
    }

    /**
     * @return Collection<int, TenancyReview>
     */
    public function getPublishedTenancyReviews(): Collection
    {
        return $this->getTenancyReviews()->filter(function (TenancyReview $tenancyReview) {
            return $tenancyReview->isPublished();
        });
    }

    /**
     * @return Collection<int, TenancyReview>
     */
    public function getPublishedTenancyReviewsWithPublishedAgency(): Collection
    {
        return $this->getTenancyReviews()->filter(function (TenancyReview $tenancyReview) {
            return $tenancyReview->isPublished() && null !== $tenancyReview->getAgency() && $tenancyReview->getAgency()->isPublished();
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
        $image->setLocale($this);

        return $this;
    }
}
