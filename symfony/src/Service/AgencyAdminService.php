<?php

namespace App\Service;

use App\Exception\NotFoundException;
use App\Factory\AgencyAdminFactory;
use App\Model\AgencyAdmin\Home;
use App\Repository\AgencyRepository;
use function sprintf;
use Symfony\Component\Security\Core\User\UserInterface;

class AgencyAdminService
{
    private UserService $userService;
    private AgencyAdminFactory $agencyAdminFactory;
    private AgencyRepository $agencyRepository;

    public function __construct(
        UserService $userService,
        AgencyAdminFactory $agencyAdminFactory,
        AgencyRepository $agencyRepository
    ) {
        $this->userService = $userService;
        $this->agencyAdminFactory = $agencyAdminFactory;
        $this->agencyRepository = $agencyRepository;
    }

    public function getHomeForUser(?UserInterface $user): Home
    {
        $user = $this->userService->getEntityFromInterface($user);

        $agency = $user->getAdminAgency();

        if (null === $agency) {
            throw new NotFoundException(sprintf('User is not an agency admin.'));
        }

        return $this->agencyAdminFactory->getHome($agency);
    }
}
