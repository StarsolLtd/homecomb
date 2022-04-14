<?php

namespace App\Repository;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\User;

interface BranchRepositoryInterface
{
    public function findOnePublishedBySlug(string $slug): Branch;

    public function findOnePublishedById(int $id): Branch;

    public function findOneBySlugUserCanManage(string $slug, User $user): Branch;

    public function findOneByNameAndAgencyOrNull(string $name, Agency $agency): ?Branch;

    public function findOneByNameWithoutAgencyOrNull(string $name): ?Branch;
}
