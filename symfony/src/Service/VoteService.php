<?php

namespace App\Service;

use App\Exception\UnexpectedValueException;
use App\Factory\VoteFactory;
use App\Model\Interaction\RequestDetails;
use App\Model\Vote\SubmitInput;
use App\Model\Vote\SubmitOutput;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class VoteService
{
    private EntityManagerInterface $entityManager;
    private InteractionService $interactionService;
    private UserService $userService;
    private VoteFactory $voteFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        InteractionService $interactionService,
        UserService $userService,
        VoteFactory $voteFactory
    ) {
        $this->entityManager = $entityManager;
        $this->interactionService = $interactionService;
        $this->userService = $userService;
        $this->voteFactory = $voteFactory;
    }

    public function vote(
        SubmitInput $submitInput,
        UserInterface $user,
        ?RequestDetails $requestDetails
    ): SubmitOutput {
        $userEntity = $this->userService->getEntityFromInterface($user);

        $vote = $this->voteFactory->createEntityFromSubmitInput($submitInput, $userEntity);

        $this->entityManager->persist($vote);
        $this->entityManager->flush();

        if (null !== $requestDetails) {
            try {
                $this->interactionService->record(
                    'Vote',
                    $vote->getId(),
                    $requestDetails,
                    $user
                );
            } catch (UnexpectedValueException $e) {
                // Shrug shoulders
            }
        }

        return new SubmitOutput(true);
    }
}
