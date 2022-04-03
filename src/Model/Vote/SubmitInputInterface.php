<?php

namespace App\Model\Vote;

interface SubmitInputInterface
{
    public function getEntityName(): string;

    public function getEntityId(): int;

    public function isPositive(): bool;
}
