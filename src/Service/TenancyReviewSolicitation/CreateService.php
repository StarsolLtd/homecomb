<?php

namespace App\Service\TenancyReviewSolicitation;

use App\Factory\TenancyReviewSolicitationFactory;
use App\Model\TenancyReviewSolicitation\CreateInputInterface;
use App\Model\TenancyReviewSolicitation\CreateReviewSolicitationOutput;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CreateService
{
    public function __construct(
        private SendService $sendService,
        private UserService $userService,
        private TenancyReviewSolicitationFactory $tenancyReviewSolicitationFactory,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function createAndSend(
        CreateInputInterface $input,
        ?UserInterface $user
    ): CreateReviewSolicitationOutput {
        $user = $this->userService->getEntityFromInterface($user);

        $tenancyReviewSolicitation = $this->tenancyReviewSolicitationFactory->createEntityFromInput($input, $user);
        $this->entityManager->persist($tenancyReviewSolicitation);
        $this->entityManager->flush();

        $this->sendService->send($tenancyReviewSolicitation, $user);

        return new CreateReviewSolicitationOutput(true);
    }
}
