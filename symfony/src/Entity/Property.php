<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PropertyRepository")
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
    private string $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $postcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $countryCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $vendorPropertyId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private bool $published = true;

    /**
     * @var Collection<int, Review>
     * @ORM\OneToMany(targetEntity="Review", mappedBy="property")
     */
    private Collection $reviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
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

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

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

    public function getVendorPropertyId(): ?string
    {
        return $this->vendorPropertyId;
    }

    public function setVendorPropertyId(?string $vendorPropertyId): self
    {
        $this->vendorPropertyId = $vendorPropertyId;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
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
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getPublishedReviews()
    {
        return $this->getReviews()->filter(function (Review $review) {
            return $review->isPublished();
        });
    }
}
