<?php

namespace App\Service;

use App\Exception\UnexpectedValueException;
use App\Factory\FlagFactory;
use App\Model\Flag\SubmitInput;
use App\Model\Flag\SubmitOutput;
use App\Model\Interaction\RequestDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FlagService
{
    private EntityManagerInterface $entityManager;
    private InteractionService $interactionService;
    private NotificationService $notificationService;
    private UserService $userService;
    private FlagFactory $flagFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        InteractionService $interactionService,
        NotificationService $notificationService,
        UserService $userService,
        FlagFactory $flagFactory
    ) {
        $this->entityManager = $entityManager;
        $this->interactionService = $interactionService;
        $this->notificationService = $notificationService;
        $this->userService = $userService;
        $this->flagFactory = $flagFactory;
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

        if (null !== $requestDetails) {
            try {
                $this->interactionService->record(
                    'Flag',
                    $flag->getId(),
                    $requestDetails,
                    $user
                );
            } catch (UnexpectedValueException $e) {
                // Shrug shoulders
            }
        }

        return new SubmitOutput(true);
    }
}
