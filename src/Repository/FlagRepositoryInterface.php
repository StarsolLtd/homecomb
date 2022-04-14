<?php

namespace App\Repository;

use App\Entity\Flag\Flag;

interface FlagRepositoryInterface
{
    public function findOneById(int $id): Flag;
}
