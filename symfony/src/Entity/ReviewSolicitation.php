<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReviewSolicationRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class ReviewSolicitation
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
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="sender_user_id", referencedColumnName="id", nullable=false)
     */
    private User $senderUser;

    /**
     * @ORM\ManyToOne(targetEntity="Branch")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id", nullable=false)
     */
    private Branch $branch;

    /**
     * @ORM\ManyToOne(targetEntity="Property")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id", nullable=false)
     */
    private Property $property;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $recipientTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $recipientFirstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $recipientLastName;

    /**
     * @ORM\OneToOne(targetEntity="Review")
     * @ORM\JoinColumn(name="review_id", referencedColumnName="id")
     */
    private ?Review $review = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSenderUser(): User
    {
        return $this->senderUser;
    }

    public function setSenderUser(User $senderUser): self
    {
        $this->senderUser = $senderUser;

        return $this;
    }

    public function getBranch(): Branch
    {
        return $this->branch;
    }

    public function setBranch(Branch $branch): self
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

    public function getRecipientTitle(): ?string
    {
        return $this->recipientTitle;
    }

    public function setRecipientTitle(?string $recipientTitle): self
    {
        $this->recipientTitle = $recipientTitle;

        return $this;
    }

    public function getRecipientFirstName(): string
    {
        return $this->recipientFirstName;
    }

    public function setRecipientFirstName(string $recipientFirstName): self
    {
        $this->recipientFirstName = $recipientFirstName;

        return $this;
    }

    public function getRecipientLastName(): string
    {
        return $this->recipientLastName;
    }

    public function setRecipientLastName(string $recipientLastName): self
    {
        $this->recipientLastName = $recipientLastName;

        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): self
    {
        $this->review = $review;

        return $this;
    }
}
