<?php

namespace App\Entity;

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
    private int $id;

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

    public function getProperty(): ?Property
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
}
