<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AgencyRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Agency
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $postcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $countryCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $externalUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private bool $published = false;

    /**
     * @var Collection<int, Branch>
     * @ORM\OneToMany(targetEntity="Branch", mappedBy="agency", cascade={"persist"})
     */
    private Collection $branches;

    /**
     * @var Collection<int, Image>
     * @ORM\OneToMany(targetEntity="Image", mappedBy="agency", cascade={"persist"})
     */
    private Collection $images;

    /**
     * @var Collection<int, User>
     * @ORM\OneToMany(targetEntity="User", mappedBy="adminAgency")
     */
    private Collection $adminUsers;

    public function __construct()
    {
        $this->branches = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->adminUsers = new ArrayCollection();
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

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getExternalUrl(): ?string
    {
        return $this->externalUrl;
    }

    public function setExternalUrl(?string $externalUrl): self
    {
        $this->externalUrl = $externalUrl;

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
     * @return Collection<int, Branch>
     */
    public function getBranches(): Collection
    {
        return $this->branches;
    }

    public function addBranch(Branch $branch): self
    {
        if ($this->branches->contains($branch)) {
            return $this;
        }
        $this->branches[] = $branch;
        $branch->setAgency($this);

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
        $image->setAgency($this);

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getLogoImages(): Collection
    {
        return $this->getImages()->filter(function (Image $image) {
            return Image::TYPE_LOGO === $image->getType();
        });
    }

    public function getLogoImage(): ?Image
    {
        $logoImage = $this->getLogoImages()->first();
        if (false === $logoImage) {
            return null;
        }

        return $logoImage;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAdminUsers(): Collection
    {
        return $this->adminUsers;
    }

    public function addAdminUser(User $user): self
    {
        if ($this->adminUsers->contains($user)) {
            return $this;
        }
        $this->adminUsers[] = $user;
        $user->setAdminAgency($this);

        return $this;
    }
}
