<?php

namespace App\Factory\Survey;

use App\Entity\Survey\Response;
use App\Entity\Survey\Survey;
use App\Entity\User;

class ResponseFactory
{
    public function createEntity(Survey $survey, ?User $user): Response
    {
        return (new Response())
            ->setSurvey($survey)
            ->setUser($user)
        ;
    }
}
