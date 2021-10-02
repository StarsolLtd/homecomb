<?php

namespace App\Service\TenancyReviewSolicitation;

use App\Factory\TenancyReviewSolicitationFactory;
use App\Model\TenancyReviewSolicitation\FormData;
use App\Service\User\UserService;
use Symfony\Component\Security\Core\User\UserInterface;

class GetFormDataService
{
    public function __construct(
        private UserService $userService,
        private TenancyReviewSolicitationFactory $tenancyReviewSolicitationFactory,
    ) {
    }

    public function getFormData(?UserInterface $user): FormData
    {
        $user = $this->userService->getEntityFromInterface($user);

        return $this->tenancyReviewSolicitationFactory->createFormDataModelFromUser($user);
    }
}
