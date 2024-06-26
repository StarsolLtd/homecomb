<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BranchRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 * @ORM\Table(
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="branch_unique", columns={"agency_id", "name"})
 *    }
 * )
 */
class Branch
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
     * @ORM\ManyToOne(targetEntity="Agency", inversedBy="branches")
     * @ORM\JoinColumn(name="agency_id", referencedColumnName="id", nullable=true)
     */
    private ?Agency $agency = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name = '';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $telephone = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private bool $published = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $slug;

    /**
     * @var Collection<int, TenancyReview>
     * @ORM\OneToMany(targetEntity="TenancyReview", mappedBy="branch")
     */
    private Collection $tenancyReviews;

    /**
     * @var Collection<int, Image>
     * @ORM\OneToMany(targetEntity="Image", mappedBy="branch")
     */
    private Collection $images;

    public function __construct()
    {
        $this->tenancyReviews = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function __toString(): string
    {
        return implode(', ', [$this->getAgency(), $this->getName()]);
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAgency(): ?Agency
    {
        return $this->agency;
    }

    public function setAgency(?Agency $agency): self
    {
        $this->agency = $agency;

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
        $tenancyReview->setBranch($this);

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
        $image->setBranch($this);

        return $this;
    }
}
