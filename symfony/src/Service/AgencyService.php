<?php

namespace App\Service;

use App\Entity\Agency;
use App\Factory\AgencyFactory;
use App\Model\Agency\AgencyView;
use App\Model\Agency\CreateAgencyInput;
use App\Model\Agency\CreateAgencyOutput;
use App\Repository\AgencyRepository;
use App\Util\AgencyHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AgencyService
{
    private NotificationService $notificationService;
    private UserService $userService;
    private EntityManagerInterface $entityManager;
    private AgencyFactory $agencyFactory;
    private AgencyHelper $agencyHelper;
    private AgencyRepository $agencyRepository;

    public function __construct(
        NotificationService $notificationService,
        UserService $userService,
        EntityManagerInterface $entityManager,
        AgencyFactory $agencyFactory,
        AgencyHelper $agencyHelper,
        AgencyRepository $agencyRepository
    ) {
        $this->notificationService = $notificationService;
        $this->userService = $userService;
        $this->entityManager = $entityManager;
        $this->agencyFactory = $agencyFactory;
        $this->agencyHelper = $agencyHelper;
        $this->agencyRepository = $agencyRepository;
    }

    public function createAgency(CreateAgencyInput $createAgencyInput, ?UserInterface $user): CreateAgencyOutput
    {
        $user = $this->userService->getEntityFromInterface($user);

        $agency = $this->agencyFactory->createAgencyEntityFromCreateAgencyInputModel($createAgencyInput);
        $agency->addAdminUser($user);

        $this->entityManager->persist($agency);
        $this->entityManager->flush();

        $this->notificationService->sendAgencyModerationNotification($agency);

        return new CreateAgencyOutput(true);
    }

    public function getViewBySlug(string $agencySlug): AgencyView
    {
        $agency = $this->agencyRepository->findOnePublishedBySlug($agencySlug);

        return $this->agencyFactory->createViewFromEntity($agency);
    }

    public function findOrCreateByName(string $agencyName): Agency
    {
        $agency = $this->agencyRepository->findOneBy(
            [
                'name' => $agencyName,
            ]
        );
        if (null !== $agency) {
            return $agency;
        }

        $agency = $this->create($agencyName);

        $this->entityManager->persist($agency);
        $this->entityManager->flush();

        return $agency;
    }

    private function create(string $agencyName): Agency
    {
        $agency = (new Agency())
            ->setName($agencyName);

        $this->agencyHelper->generateSlug($agency);

        return $agency;
    }
}
