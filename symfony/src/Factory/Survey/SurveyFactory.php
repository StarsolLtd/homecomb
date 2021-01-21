<?php

namespace App\Factory\Survey;

use App\Entity\Survey\Survey as SurveyEntity;
use App\Model\Survey\View as View;

class SurveyFactory
{
    private QuestionFactory $questionFactory;

    public function __construct(
        QuestionFactory $questionFactory
    ) {
        $this->questionFactory = $questionFactory;
    }

    public function createViewFromEntity(SurveyEntity $entity): View
    {
        $questions = [];

        foreach ($entity->getPublishedQuestions() as $question) {
            $questions[] = $this->questionFactory->createViewFromEntity($question);
        }

        return new View(
            $entity->getSlug(),
            $entity->getTitle(),
            $entity->getDescription(),
            $questions
        );
    }
}
