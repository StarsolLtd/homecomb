<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Vote\CommentVote;
use App\Entity\Vote\LocaleReviewVote;
use App\Entity\Vote\TenancyReviewVote;
use App\Entity\Vote\Vote;

interface VoteRepositoryInterface
{
    public function findOneById(int $id): Vote;

    public function findOneLocaleReviewVoteByUserAndEntity(User $user, int $entityId): ?LocaleReviewVote;

    public function findOneTenancyReviewVoteByUserAndEntity(User $user, int $entityId): ?TenancyReviewVote;

    public function findOneCommentVoteByUserAndEntity(User $user, int $entityId): ?CommentVote;
}
