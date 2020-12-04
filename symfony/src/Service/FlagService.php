<?php

namespace App\Service;

use App\Entity\Flag;
use App\Exception\UnexpectedValueException;
use App\Model\Flag\SubmitInput;
use App\Model\Flag\SubmitOutput;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use function in_array;

class FlagService
{
    private const VALID_ENTITY_NAMES = ['Agency', 'Branch', 'Property', 'Review'];

    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function submitFlag(SubmitInput $submitInput): SubmitOutput
    {
        $entityName = $submitInput->getEntityName();

        if (!in_array($entityName, self::VALID_ENTITY_NAMES)) {
            throw new UnexpectedValueException(sprintf('%s is not a valid flag entity name.', $entityName));
        }

        // TODO find or create user

        $flag = (new Flag())
            ->setEntityName($submitInput->getEntityName())
            ->setEntityId($submitInput->getEntityId())
            ->setContent($submitInput->getContent())
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());

        $this->entityManager->persist($flag);
        $this->entityManager->flush();

        return new SubmitOutput(true);
    }
}
