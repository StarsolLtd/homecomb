<?php

namespace App\Model\Vote;

class SubmitOutput
{
    private bool $success;
    private ?string $entityName;
    private ?int $entityId;
    private ?int $positiveVotes;
    private ?int $negativeVotes;
    private ?int $votesScore;

    public function __construct(
        bool $success,
        ?string $entityName = null,
        ?int $entityId = null,
        ?int $positiveVotes = null,
        ?int $negativeVotes = null,
        ?int $votesScore = null
    ) {
        $this->success = $success;
        $this->entityName = $entityName;
        $this->entityId = $entityId;
        $this->positiveVotes = $positiveVotes;
        $this->negativeVotes = $negativeVotes;
        $this->votesScore = $votesScore;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function getEntityName(): ?string
    {
        return $this->entityName;
    }

    public function getNegativeVotes(): ?int
    {
        return $this->negativeVotes;
    }

    public function getPositiveVotes(): ?int
    {
        return $this->positiveVotes;
    }

    public function getVotesScore(): ?int
    {
        return $this->votesScore;
    }
}
