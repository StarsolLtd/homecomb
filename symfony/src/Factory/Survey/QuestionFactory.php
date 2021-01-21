<?php

namespace App\Factory\Survey;

use App\Entity\Survey\Question as QuestionEntity;
use App\Model\Survey\Question as QuestionModel;

class QuestionFactory
{
    public function createViewFromEntity(QuestionEntity $entity): QuestionModel
    {
        return new QuestionModel(
            $entity->getId(),
            $entity->getType(),
            $entity->getContent(),
            $entity->getHelp(),
            $entity->getHighMeaning(),
            $entity->getLowMeaning(),
            $entity->getSortOrder()
        );
    }
}
