<?php

namespace App\Model\TenancyReview;

use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;
use App\Model\Comment\Flat as FlatComment;
use App\Model\Property\Flat as FlatProperty;
use DateTime;

class View
{
    private ?FlatBranch $branch;
    private ?FlatAgency $agency;
    private ?FlatProperty $property;
    private int $id;
    private string $author;
    private ?DateTime $start;
    private ?DateTime $end;
    private string $title;
    private string $content;
    private Stars $stars;
    private DateTime $createdAt;
    private array $comments;
    private int $positiveVotes;
    private int $negativeVotes;
    private int $votesScore;

    public function __construct(
        ?FlatBranch $branch,
        ?FlatAgency $agency,
        ?FlatProperty $property,
        int $id,
        string $author,
        ?DateTime $start,
        ?DateTime $end,
        string $title,
        string $content,
        Stars $stars,
        DateTime $createdAt,
        array $comments,
        int $positiveVotes,
        int $negativeVotes,
        int $votesScore
    ) {
        $this->branch = $branch;
        $this->agency = $agency;
        $this->property = $property;
        $this->id = $id;
        $this->author = $author;
        $this->start = $start;
        $this->end = $end;
        $this->title = $title;
        $this->content = $content;
        $this->stars = $stars;
        $this->createdAt = $createdAt;
        $this->comments = $comments;
        $this->positiveVotes = $positiveVotes;
        $this->negativeVotes = $negativeVotes;
        $this->votesScore = $votesScore;
    }

    public function getBranch(): ?FlatBranch
    {
        return $this->branch;
    }

    public function getAgency(): ?FlatAgency
    {
        return $this->agency;
    }

    public function getProperty(): ?FlatProperty
    {
        return $this->property;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getStart(): ?DateTime
    {
        return $this->start;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStars(): Stars
    {
        return $this->stars;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return FlatComment[]
     */
    public function getComments(): array
    {
        return $this->comments;
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
