<?php

namespace App\Repository\Survey;

use App\Entity\Survey\Response;

interface ResponseRepositoryInterface
{
    public function findOneById(int $id): Response;
}
