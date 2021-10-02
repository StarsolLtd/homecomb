<?php

namespace App\Service;

use App\Factory\TenancyReviewSolicitationFactory;
use App\Model\TenancyReviewSolicitation\CreateReviewSolicitationInput;
use App\Model\TenancyReviewSolicitation\CreateReviewSolicitationOutput;
use App\Model\TenancyReviewSolicitation\FormData;
use App\Model\TenancyReviewSolicitation\View;
use App\Repository\TenancyReviewSolicitationRepository;
use App\Service\TenancyReviewSolicitation\SendService;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TenancyReviewSolicitationService
{
    public function __construct(
        private SendService $sendService,
        private UserService $userService,
        private TenancyReviewSolicitationFactory $tenancyReviewSolicitationFactory,
        private TenancyReviewSolicitationRepository $tenancyReviewSolicitationRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getFormData(?UserInterface $user): FormData
    {
        $user = $this->userService->getEntityFromInterface($user);

        return $this->tenancyReviewSolicitationFactory->createFormDataModelFromUser($user);
    }

    public function createAndSend(CreateReviewSolicitationInput $input, ?UserInterface $user): CreateReviewSolicitationOutput
    {
        $user = $this->userService->getEntityFromInterface($user);

        $tenancyReviewSolicitation = $this->tenancyReviewSolicitationFactory->createEntityFromInput($input, $user);
        $this->entityManager->persist($tenancyReviewSolicitation);
        $this->entityManager->flush();

        $this->sendService->send($tenancyReviewSolicitation, $user);

        return new CreateReviewSolicitationOutput(true);
    }

    public function getViewByCode(string $code): View
    {
        $rs = $this->tenancyReviewSolicitationRepository->findOneUnfinishedByCode($code);

        return $this->tenancyReviewSolicitationFactory->createViewByEntity($rs);
    }
}
