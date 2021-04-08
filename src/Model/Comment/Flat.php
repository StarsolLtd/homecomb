<?php

namespace App\Model\Comment;

use DateTime;

class Flat
{
    private int $id;
    private string $author;
    private string $content;
    private DateTime $createdAt;

    public function __construct(
        int $id,
        string $author,
        string $content,
        DateTime $createdAt
    ) {
        $this->id = $id;
        $this->author = $author;
        $this->content = $content;
        $this->createdAt = $createdAt;
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
