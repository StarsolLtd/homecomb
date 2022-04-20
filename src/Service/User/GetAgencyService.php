<?php

namespace App\Service\User;

use App\Exception\NotFoundException;
use App\Factory\FlatModelFactory;
use App\Model\Agency\Flat;
use Symfony\Component\Security\Core\User\UserInterface;

class GetAgencyService
{
    public function __construct(
        private UserService $userService,
        private FlatModelFactory $flatModelFactory,
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
}
