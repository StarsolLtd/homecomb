<?php

namespace App\Factory\Survey;

use App\Entity\Survey\Question as QuestionEntity;
use App\Model\Survey\Question as QuestionModel;

class QuestionFactory
{
    public function __construct(
        private ChoiceFactory $choiceFactory
    ) {
    }

    public function createModelFromEntity(QuestionEntity $entity): QuestionModel
    {
        $choices = [];

        foreach ($entity->getPublishedChoices() as $choice) {
            $choices[] = $this->choiceFactory->createModelFromEntity($choice);
        }

        return new QuestionModel(
            $entity->getId(),
            $entity->getType(),
            $entity->getContent(),
            $entity->getHelp(),
            $entity->getHighMeaning(),
            $entity->getLowMeaning(),
            $entity->getSortOrder(),
            $choices
        );
    }
}
