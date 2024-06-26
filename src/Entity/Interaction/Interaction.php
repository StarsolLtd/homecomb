<?php

namespace App\Entity\Interaction;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="entity_name", type="string")
 * @ORM\DiscriminatorMap({
 *     "Answer" = "AnswerInteraction",
 *     "BroadbandProviderReview" = "BroadbandProviderReviewInteraction",
 *     "Flag" = "FlagInteraction",
 *     "Review" = "TenancyReviewInteraction",
 *     "Vote" = "VoteInteraction"
 * })
 */
abstract class Interaction
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

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
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $sessionId = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $ipAddress = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $userAgent = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(?string $sessionId): self
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }
}
