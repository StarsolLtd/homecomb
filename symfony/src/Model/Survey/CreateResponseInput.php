<?php

namespace App\Model\Survey;

class CreateResponseInput
{
    private string $surveySlug;

    public function __construct(
        string $surveySlug
    ) {
        $this->surveySlug = $surveySlug;
    }

    public function getSurveySlug(): string
    {
        return $this->surveySlug;
    }
}
