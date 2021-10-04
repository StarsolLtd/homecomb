<?php

namespace App\Service;

use App\Entity\Agency;
use App\Exception\NotFoundException;
use App\Factory\FlatModelFactory;
use App\Model\Agency\Flat;
use App\Repository\AgencyRepository;
use App\Service\User\UserService;
use App\Util\AgencyHelper;
use Doctrine\ORM\EntityManagerInterface;
use function sprintf;
use Symfony\Component\Security\Core\User\UserInterface;

class AgencyService
{
    public function __construct(
        private UserService $userService,
        private EntityManagerInterface $entityManager,
        private FlatModelFactory $flatModelFactory,
        private AgencyHelper $agencyHelper,
        private AgencyRepository $agencyRepository
    ) {
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
