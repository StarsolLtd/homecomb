<?php

namespace App\Service\Agency;

use App\Exception\ConflictException;
use App\Factory\AgencyFactory;
use App\Model\Agency\CreateAgencyOutput;
use App\Model\Agency\CreateInputInterface;
use App\Service\NotificationService;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CreateService
{
    public function __construct(
        private NotificationService $notificationService,
        private UserService $userService,
        private EntityManagerInterface $entityManager,
        private AgencyFactory $agencyFactory,
    ) {
    }

    public function createAgency(
        CreateInputInterface $createInput,
        ?UserInterface $user
    ): CreateAgencyOutput {
        $user = $this->userService->getEntityFromInterface($user);

        if (null !== $user->getAdminAgency()) {
            throw new ConflictException('User is already an agency admin.');
        }

        $agency = $this->agencyFactory->createAgencyEntityFromCreateAgencyInputModel($createInput);
        $agency->addAdminUser($user);

        $this->entityManager->persist($agency);
        $this->entityManager->flush();

        $this->notificationService->sendAgencyModerationNotification($agency);

        return new CreateAgencyOutput(true);
    }
}
