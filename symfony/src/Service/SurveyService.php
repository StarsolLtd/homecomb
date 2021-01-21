<?php

namespace App\Service;

use App\Factory\Survey\SurveyFactory;
use App\Model\Survey\View;
use App\Repository\Survey\SurveyRepository;

class SurveyService
{
    private SurveyFactory $surveyFactory;
    private SurveyRepository $surveyRepository;

    public function __construct(
        SurveyFactory $surveyFactory,
        SurveyRepository $surveyRepository
    ) {
        $this->surveyFactory = $surveyFactory;
        $this->surveyRepository = $surveyRepository;
    }

    public function getViewBySlug(string $slug): View
    {
        $survey = $this->surveyRepository->findOnePublishedBySlug($slug);

        return $this->surveyFactory->createViewFromEntity($survey);
    }
}
