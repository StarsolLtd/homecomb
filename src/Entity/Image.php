<?php

namespace App\Entity;

use App\Entity\Locale\Locale;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Image
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    public const TYPE_LOGO = 'logo';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $image;

    /**
     * @Vich\UploadableField(mapping="image_images", fileNameProperty="image")
     */
    private File $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity="Agency", inversedBy="images")
     * @ORM\JoinColumn(name="agency_id", referencedColumnName="id", nullable=true)
     */
    private ?Agency $agency = null;

    /**
     * @ORM\ManyToOne(targetEntity="Branch", inversedBy="images")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id", nullable=true)
     */
    private ?Branch $branch = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Locale\Locale", inversedBy="images")
     * @ORM\JoinColumn(name="locale_id", referencedColumnName="id", nullable=true)
     */
    private ?Locale $locale = null;

    /**
     * @ORM\ManyToOne(targetEntity="TenancyReview", inversedBy="images")
     * @ORM\JoinColumn(name="tenancy_review_id", referencedColumnName="id", nullable=true)
     */
    private ?TenancyReview $tenancyReview = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="images")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setImageFile(File $image): self
    {
        $this->imageFile = $image;
        $this->updatedAt = new DateTime();

        return $this;
    }

    public function getImageFile(): File
    {
        return $this->imageFile;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
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

    public function getBranch(): ?Branch
    {
        return $this->branch;
    }

    public function setBranch(?Branch $branch): self
    {
        $this->branch = $branch;

        return $this;
    }

    public function getLocale(): ?Locale
    {
        return $this->locale;
    }

    public function setLocale(?Locale $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTenancyReview(): ?TenancyReview
    {
        return $this->tenancyReview;
    }

    public function setTenancyReview(?TenancyReview $tenancyReview): self
    {
        $this->tenancyReview = $tenancyReview;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
