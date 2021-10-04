<?php

namespace App\Service;

use App\Factory\FlagFactory;
use App\Model\Flag\SubmitInput;
use App\Model\Flag\SubmitOutput;
use App\Model\Interaction\RequestDetails;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FlagService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private InteractionService $interactionService,
        private NotificationService $notificationService,
        private UserService $userService,
        private FlagFactory $flagFactory
    ) {
    }

    public function submitFlag(
        SubmitInput $submitInput,
        ?UserInterface $user,
        ?RequestDetails $requestDetails = null
    ): SubmitOutput {
        $userEntity = $this->userService->getUserEntityOrNullFromUserInterface($user);

        $flag = $this->flagFactory->createEntityFromSubmitInput($submitInput, $userEntity);

        $this->entityManager->persist($flag);
        $this->entityManager->flush();

        $this->notificationService->sendFlagModerationNotification($flag);

        $this->interactionService->record('Flag', $flag->getId(), $requestDetails, $user);

        return new SubmitOutput(true);
    }
}
