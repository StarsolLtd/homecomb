<?php

namespace App\Repository\Survey;

use App\Entity\Survey\Choice;

interface ChoiceRepositoryInterface
{
    public function findOnePublishedById(int $id): Choice;
}
