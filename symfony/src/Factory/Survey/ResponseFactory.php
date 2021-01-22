<?php

namespace App\Factory\Survey;

use App\Entity\Survey\Response;
use App\Entity\User;
use App\Model\Survey\CreateResponseInput;
use App\Repository\Survey\SurveyRepository;

class ResponseFactory
{
    private SurveyRepository $surveyRepository;

    public function __construct(
        SurveyRepository $surveyRepository
    ) {
        $this->surveyRepository = $surveyRepository;
    }

    public function createEntityFromCreateInput(CreateResponseInput $input, ?User $user): Response
    {
        $question = $this->surveyRepository->findOnePublishedBySlug($input->getSurveySlug());

        return (new Response())
            ->setSurvey($question)
            ->setUser($user)
        ;
    }
}
