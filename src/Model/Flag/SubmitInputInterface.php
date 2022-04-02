<?php

namespace App\Model\Flag;

interface SubmitInputInterface
{
    public function getEntityName(): string;

    public function getEntityId(): int;

    public function getContent(): ?string;
}
