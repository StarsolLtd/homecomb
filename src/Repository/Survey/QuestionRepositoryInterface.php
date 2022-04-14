<?php

namespace App\Repository\Survey;

use App\Entity\Survey\Question;

interface QuestionRepositoryInterface
{
    public function findOnePublishedById(int $id): Question;

    public function findLastPublished(): ?Question;
}
