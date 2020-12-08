<?php

namespace App\Service;

use App\Entity\Flag;
use App\Exception\UnexpectedValueException;
use App\Model\Flag\SubmitInput;
use App\Model\Flag\SubmitOutput;
use Doctrine\ORM\EntityManagerInterface;
use function in_array;
use Symfony\Component\Security\Core\User\UserInterface;

class FlagService
{
    private const VALID_ENTITY_NAMES = ['Agency', 'Branch', 'Property', 'Review'];

    private EntityManagerInterface $entityManager;
    private NotificationService $notificationService;
    private UserService $userService;

    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationService $notificationService,
        UserService $userService
    ) {
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;
        $this->userService = $userService;
    }

    public function submitFlag(SubmitInput $submitInput, ?UserInterface $user): SubmitOutput
    {
        $entityName = $submitInput->getEntityName();

        if (!in_array($entityName, self::VALID_ENTITY_NAMES)) {
            throw new UnexpectedValueException(sprintf('%s is not a valid flag entity name.', $entityName));
        }

        $flag = (new Flag())
            ->setEntityName($submitInput->getEntityName())
            ->setEntityId($submitInput->getEntityId())
            ->setContent($submitInput->getContent())
            ->setUser($this->userService->getUserEntityFromUserInterface($user));

        $this->entityManager->persist($flag);
        $this->entityManager->flush();

        $this->notificationService->sendFlagModerationNotification($flag);

        return new SubmitOutput(true);
    }
}
