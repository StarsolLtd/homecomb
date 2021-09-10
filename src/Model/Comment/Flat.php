<?php

namespace App\Model\Comment;

use DateTime;

class Flat
{
    public function __construct(
        private int $id,
        private string $author,
        private string $content,
        private DateTime $createdAt,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
