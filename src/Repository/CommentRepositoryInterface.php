<?php

namespace App\Repository;

use App\Entity\Comment\Comment;

interface CommentRepositoryInterface
{
    public function findOnePublishedById(int $id): Comment;

    public function findLastPublished(): ?Comment;
}
