<?php

namespace App\Service;

use App\Entity\Agency;
use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Factory\AgencyFactory;
use App\Factory\FlatModelFactory;
use App\Model\Agency\CreateAgencyInput;
use App\Model\Agency\CreateAgencyOutput;
use App\Model\Agency\Flat;
use App\Model\Agency\UpdateAgencyInput;
use App\Model\Agency\UpdateAgencyOutput;
use App\Repository\AgencyRepository;
use App\Service\User\UserService;
use App\Util\AgencyHelper;
use Doctrine\ORM\EntityManagerInterface;
use function sprintf;
use Symfony\Component\Security\Core\User\UserInterface;

class AgencyService
{
    public function __construct(
        private NotificationService $notificationService,
        private UserService $userService,
        private EntityManagerInterface $entityManager,
        private AgencyFactory $agencyFactory,
        private FlatModelFactory $flatModelFactory,
        private AgencyHelper $agencyHelper,
        private AgencyRepository $agencyRepository
    ) {
    }

    public function createAgency(CreateAgencyInput $createAgencyInput, ?UserInterface $user): CreateAgencyOutput
    {
        $user = $this->userService->getEntityFromInterface($user);

        if (null !== $user->getAdminAgency()) {
            throw new ConflictException(sprintf('User is already an agency admin.'));
        }

        $agency = $this->agencyFactory->createAgencyEntityFromCreateAgencyInputModel($createAgencyInput);
        $agency->addAdminUser($user);

        $this->entityManager->persist($agency);
        $this->entityManager->flush();

        $this->notificationService->sendAgencyModerationNotification($agency);

        return new CreateAgencyOutput(true);
    }

    public function getAgencyForUser(?UserInterface $user): Flat
    {
        $user = $this->userService->getEntityFromInterface($user);

        $agency = $user->getAdminAgency();

        if (null === $agency) {
            throw new NotFoundException(sprintf('User is not an agency admin.'));
        }

        return $this->flatModelFactory->getAgencyFlatModel($agency);
    }

    public function updateAgency(string $slug, UpdateAgencyInput $updateAgencyInput, ?UserInterface $user): UpdateAgencyOutput
    {
        $user = $this->userService->getEntityFromInterface($user);

        $agency = $user->getAdminAgency();

        if (null === $agency) {
            throw new NotFoundException('No agency found for user.');
        }
        if ($agency->getSlug() !== $slug) {
            throw new ForbiddenException('User does not have permission to manage agency.');
        }

        $agency->setExternalUrl($updateAgencyInput->getExternalUrl())
            ->setPostcode($updateAgencyInput->getPostcode());

        $this->entityManager->flush();

        return new UpdateAgencyOutput(true);
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

        $agency->setSlug($this->agencyHelper->generateSlug($agency));

        return $agency;
    }
}
