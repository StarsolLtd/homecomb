<?php

namespace App\Service\Agency;

use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Model\Agency\UpdateAgencyOutput;
use App\Model\Agency\UpdateInputInterface;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UpdateService
{
    public function __construct(
        private UserService $userService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function updateAgency(string $slug, UpdateInputInterface $input, ?UserInterface $user): UpdateAgencyOutput
    {
        $user = $this->userService->getEntityFromInterface($user);

        $agency = $user->getAdminAgency();

        if (null === $agency) {
            throw new NotFoundException('No agency found for user.');
        }
        if ($agency->getSlug() !== $slug) {
            throw new ForbiddenException('User does not have permission to manage agency.');
        }

        $agency->setExternalUrl($input->getExternalUrl())
            ->setPostcode($input->getPostcode());

        $this->entityManager->flush();

        return new UpdateAgencyOutput(true);
    }
}
