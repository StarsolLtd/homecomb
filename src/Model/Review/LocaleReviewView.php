<?php

namespace App\Model\Review;

use DateTime;

class LocaleReviewView
{
    private string $slug;
    private ?string $author;
    private ?string $title;
    private ?string $content;
    private ?int $overallStars;
    private DateTime $createdAt;
    private int $positiveVotes;
    private int $negativeVotes;
    private int $votesScore;

    public function __construct(
        string $slug,
        ?string $author,
        ?string $title,
        ?string $content,
        ?int $overallStars,
        DateTime $createdAt,
        int $positiveVotes,
        int $negativeVotes,
        int $votesScore
    ) {
        $this->slug = $slug;
        $this->author = $author;
        $this->title = $title;
        $this->content = $content;
        $this->overallStars = $overallStars;
        $this->createdAt = $createdAt;
        $this->positiveVotes = $positiveVotes;
        $this->negativeVotes = $negativeVotes;
        $this->votesScore = $votesScore;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getOverallStars(): ?int
    {
        return $this->overallStars;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getPositiveVotes(): int
    {
        return $this->positiveVotes;
    }

    public function getNegativeVotes(): int
    {
        return $this->negativeVotes;
    }

    public function getVotesScore(): int
    {
        return $this->votesScore;
    }
}
