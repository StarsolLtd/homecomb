<?php

namespace App\Factory\Survey;

use App\Entity\Survey\Choice as ChoiceEntity;
use App\Model\Survey\Choice as ChoiceModel;

class ChoiceFactory
{
    public function createModelFromEntity(ChoiceEntity $entity): ChoiceModel
    {
        return new ChoiceModel(
            $entity->getId(),
            $entity->getName(),
            $entity->getHelp(),
            $entity->getSortOrder()
        );
    }
}
