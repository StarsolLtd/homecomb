<?php

namespace App\Service\TenancyReview;

use App\Factory\TenancyReviewFactory;
use App\Model\Interaction\RequestDetailsInterface;
use App\Model\TenancyReview\SubmitInputInterface;
use App\Model\TenancyReview\SubmitOutput;
use App\Repository\PropertyRepository;
use App\Service\Agency\FindOrCreateService as AgencyFindOrCreateService;
use App\Service\Branch\FindOrCreateService as BranchFindOrCreateService;
use App\Service\InteractionService;
use App\Service\NotificationService;
use App\Service\TenancyReviewSolicitation\CompleteService;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CreateService
{
    public function __construct(
        private AgencyFindOrCreateService $agencyFindOrCreateService,
        private BranchFindOrCreateService $branchFindOrCreateService,
        private InteractionService $interactionService,
        private NotificationService $notificationService,
        private CompleteService $completeService,
        private UserService $userService,
        private EntityManagerInterface $entityManager,
        private PropertyRepository $propertyRepository,
        private TenancyReviewFactory $tenancyReviewFactory
    ) {
    }

    public function submitReview(
        SubmitInputInterface $submitInput,
        ?UserInterface $user,
        ?RequestDetailsInterface $requestDetails = null
    ): SubmitOutput {
        $property = $this->propertyRepository->findOnePublishedBySlug($submitInput->getPropertySlug());

        $agencyName = $submitInput->getAgencyName();
        $agency = $agencyName ? $this->agencyFindOrCreateService->findOrCreateByName($agencyName) : null;
        $branchName = $submitInput->getAgencyBranch();
        $branch = $branchName ? $this->branchFindOrCreateService->findOrCreate($branchName, $agency) : null;
        $userEntity = $this->userService->getUserEntityOrNullFromUserInterface($user);

        $tenancyReview = $this->tenancyReviewFactory->createEntity($submitInput, $property, $branch, $userEntity);

        $this->entityManager->persist($tenancyReview);

        $code = $submitInput->getCode();
        if (null !== $code) {
            $this->completeService->complete($code, $tenancyReview);
        }

        $this->entityManager->flush();

        $this->notificationService->sendTenancyReviewModerationNotification($tenancyReview);

        $this->interactionService->record(InteractionService::TYPE_TENANCY_REVIEW, $tenancyReview->getId(), $requestDetails, $user);

        return new SubmitOutput(true);
    }
}
