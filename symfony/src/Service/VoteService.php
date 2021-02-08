<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Vote\Vote;
use App\Exception\UnexpectedValueException;
use App\Factory\VoteFactory;
use App\Model\Interaction\RequestDetails;
use App\Model\Vote\SubmitInput;
use App\Model\Vote\SubmitOutput;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class VoteService
{
    private EntityManagerInterface $entityManager;
    private InteractionService $interactionService;
    private UserService $userService;
    private VoteRepository $voteRepository;
    private VoteFactory $voteFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        InteractionService $interactionService,
        UserService $userService,
        VoteRepository $voteRepository,
        VoteFactory $voteFactory
    ) {
        $this->entityManager = $entityManager;
        $this->interactionService = $interactionService;
        $this->userService = $userService;
        $this->voteRepository = $voteRepository;
        $this->voteFactory = $voteFactory;
    }

    public function vote(
        SubmitInput $input,
        ?UserInterface $user,
        ?RequestDetails $requestDetails
    ): SubmitOutput {
        $userEntity = $this->userService->getEntityFromInterface($user);

        $vote = $this->findExisting($input, $userEntity);

        if (null === $vote) {
            $vote = $this->voteFactory->createEntityFromSubmitInput($input, $userEntity);
            $this->entityManager->persist($vote);
        } else {
            $vote->setPositive($input->isPositive());
        }

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

    private function findExisting(SubmitInput $submitInput, User $user): ?Vote
    {
        switch ($submitInput->getEntityName()) {
            case 'Comment':
                return $this->voteRepository->findOneCommentVoteByUserAndEntity($user, $submitInput->getEntityId());
            case 'Review':
                return $this->voteRepository->findOneReviewVoteByUserAndEntity($user, $submitInput->getEntityId());
            // @codeCoverageIgnoreStart
            default:
                return null;
        }
        // @codeCoverageIgnoreEnd
    }
}
