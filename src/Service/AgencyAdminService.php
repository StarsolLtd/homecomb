<?php

namespace App\Service;

use App\Exception\NotFoundException;
use App\Factory\AgencyAdminFactory;
use App\Factory\FlatModelFactory;
use App\Model\AgencyAdmin\Home;
use App\Model\Branch\Flat as FlatBranch;
use App\Repository\BranchRepositoryInterface;
use App\Service\User\UserService;
use Symfony\Component\Security\Core\User\UserInterface;

class AgencyAdminService
{
    public function __construct(
        private UserService $userService,
        private AgencyAdminFactory $agencyAdminFactory,
        private FlatModelFactory $flatModelFactory,
        private BranchRepositoryInterface $branchRepository,
    ) {
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

    public function getBranch(string $branchSlug, ?UserInterface $user): FlatBranch
    {
        $user = $this->userService->getEntityFromInterface($user);

        $branch = $this->branchRepository->findOneBySlugUserCanManage($branchSlug, $user);

        return $this->flatModelFactory->getBranchFlatModel($branch);
    }
}
