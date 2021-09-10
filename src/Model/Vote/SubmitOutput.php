<?php

namespace App\Model\Vote;

class SubmitOutput
{
    public function __construct(
        private bool $success,
        private ?string $entityName = null,
        private ?int $entityId = null,
        private ?int $positiveVotes = null,
        private ?int $negativeVotes = null,
        private ?int $votesScore = null,
    ) {
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
