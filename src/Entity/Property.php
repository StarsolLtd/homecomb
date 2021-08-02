<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PropertyRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Property
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $addressLine1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $addressLine2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $addressLine3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $addressLine4;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $locality;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $county;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $postcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $countryCode;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private ?float $latitude;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=8, nullable=true)
     */
    private ?float $longitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $vendorPropertyId = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $slug;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private bool $published = true;

    /**
     * @var Collection<int, TenancyReview>
     * @ORM\OneToMany(targetEntity="TenancyReview", mappedBy="property")
     */
    private Collection $tenancyReviews;

    public function __construct()
    {
        $this->tenancyReviews = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getAddressLine1().', '.$this->getPostcode();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(string $addressLine1): self
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function getAddressLine3(): ?string
    {
        return $this->addressLine3;
    }

    public function setAddressLine3(?string $addressLine3): self
    {
        $this->addressLine3 = $addressLine3;

        return $this;
    }

    public function getAddressLine4(): ?string
    {
        return $this->addressLine4;
    }

    public function setAddressLine4(?string $addressLine4): self
    {
        $this->addressLine4 = $addressLine4;

        return $this;
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function setLocality(?string $locality): self
    {
        $this->locality = $locality;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCounty(): ?string
    {
        return $this->county;
    }

    public function setCounty(?string $county): self
    {
        $this->county = $county;

        return $this;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getVendorPropertyId(): ?string
    {
        return $this->vendorPropertyId;
    }

    public function setVendorPropertyId(?string $vendorPropertyId): self
    {
        $this->vendorPropertyId = $vendorPropertyId;

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
        $this->tenancyReviews[] = $tenancyReview;
        $tenancyReview->setProperty($this);

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
}
