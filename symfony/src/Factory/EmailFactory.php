<?php

namespace App\Factory;

use App\Entity\Email;
use App\Entity\User;

class EmailFactory
{
    public function createEntity(
        string $from,
        string $to,
        string $subject,
        string $text,
        ?string $html = null,
        ?int $type = null,
        ?User $senderUser = null,
        ?User $recipientUser = null,
        ?Email $resendOfEmail = null
    ): Email {
        return (new Email())
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->setText($text)
            ->setHtml($html)
            ->setType($type)
            ->setSenderUser($senderUser)
            ->setRecipientUser($recipientUser)
            ->setResendOfEmail($resendOfEmail)
        ;
    }
}
