<?php

namespace App\Factory\Survey;

use App\Entity\Survey\Survey as SurveyEntity;
use App\Model\Survey\View as View;

class SurveyFactory
{
    public function __construct(
        private QuestionFactory $questionFactory,
    ) {
    }

    public function createViewFromEntity(SurveyEntity $entity): View
    {
        $questions = [];

        foreach ($entity->getPublishedQuestions() as $question) {
            $questions[] = $this->questionFactory->createModelFromEntity($question);
        }

        return new View(
            $entity->getSlug(),
            $entity->getTitle(),
            $entity->getDescription(),
            $questions
        );
    }
}
