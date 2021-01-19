<?php

namespace App\Entity\Flag;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FlagRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="entity_name", type="string")
 * @ORM\DiscriminatorMap({
 *     "Agency" = "AgencyFlag",
 *     "Branch" = "BranchFlag",
 *     "Property" = "PropertyFlag",
 *     "Review" = "ReviewFlag"
 * })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
abstract class Flag
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="flags")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private ?User $user = null;

    /**
     * @ORM\Column(type="integer")
     */
    private int $entityId = 0;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $content;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $valid = null;

    public function getId(): int
    {
        return $this->id;
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

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): self
    {
        $this->entityId = $entityId;

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

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }
}
