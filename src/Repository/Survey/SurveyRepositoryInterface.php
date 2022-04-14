<?php

namespace App\Repository\Survey;

use App\Entity\Survey\Survey;

interface SurveyRepositoryInterface
{
    public function findOnePublishedBySlug(string $slug): Survey;
}
