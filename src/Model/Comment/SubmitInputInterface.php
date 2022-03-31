<?php

namespace App\Model\Comment;

interface SubmitInputInterface
{
    public function getEntityName(): string;

    public function getEntityId(): int;

    public function getContent(): string;
}
