<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmailRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Email
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $type = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $from;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $to;

    /**
     * @ORM\Column(type="text", length=65535, nullable=false)
     */
    private string $subject;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $html;

    /**
     * @ORM\Column(type="text", length=65535, nullable=false)
     */
    private string $text;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="sender_user_id", referencedColumnName="id", nullable=true)
     */
    private User $senderUser;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="recipient_user_id", referencedColumnName="id", nullable=true)
     */
    private User $recipientUser;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $sentAt;

    /**
     * @ORM\OneToOne(targetEntity="Email")
     * @ORM\JoinColumn(name="resend_of_email_id", referencedColumnName="id")
     */
    private Email $resendOfEmail;

    public function getId(): ?int
    {
        return $this->id;
    }
}
