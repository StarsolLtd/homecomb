<?php

namespace App\Service\BroadbandProviderReview;

use App\Exception\UnexpectedValueException;
use App\Factory\BroadbandProviderReviewFactory;
use App\Model\BroadbandProviderReview\SubmitInput;
use App\Model\BroadbandProviderReview\SubmitOutput;
use App\Model\Interaction\RequestDetailsInterface;
use App\Repository\BroadbandProviderRepositoryInterface;
use App\Service\BroadbandProvider\FindOrCreateService as BroadbandProviderFindOrCreateService;
use App\Service\InteractionService;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CreateService
{
    public function __construct(
        private BroadbandProviderReviewFactory $broadbandProviderReviewFactory,
        private BroadbandProviderFindOrCreateService $findOrCreateService,
        private BroadbandProviderRepositoryInterface $broadbandProviderRepository,
        private InteractionService $interactionService,
        private UserService $userService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function submitReview(
        SubmitInput $submitInput,
        ?UserInterface $user,
        ?RequestDetailsInterface $requestDetails = null
    ): SubmitOutput {
        $broadbandProviderSlug = $submitInput->getBroadbandProviderSlug();
        if (null !== $broadbandProviderSlug) {
            $broadbandProvider = $this->broadbandProviderRepository->findOnePublishedBySlug($broadbandProviderSlug);
        } else {
            $broadbandProviderName = $submitInput->getBroadbandProviderName();

            if (null === $broadbandProviderName) {
                throw new UnexpectedValueException('Either a broadband provider slug or name must be supplied.');
            }

            $broadbandProvider = $this->findOrCreateService->findOrCreate($broadbandProviderName, 'UK');
        }

        $userEntity = $this->userService->getUserEntityOrNullFromUserInterface($user);

        $review = $this->broadbandProviderReviewFactory->createEntity($submitInput, $broadbandProvider, $userEntity);

        $this->entityManager->persist($review);
        $this->entityManager->flush();

        $this->interactionService->record(InteractionService::TYPE_BROADBAND_PROVIDER_REVIEW, $review->getId(), $requestDetails, $user);

        return new SubmitOutput(true);
    }
}
