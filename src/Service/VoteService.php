<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Vote\CommentVote;
use App\Entity\Vote\LocaleReviewVote;
use App\Entity\Vote\TenancyReviewVote;
use App\Entity\Vote\Vote;
use App\Factory\VoteFactory;
use App\Model\Interaction\RequestDetailsInterface;
use App\Model\Vote\SubmitInput;
use App\Model\Vote\SubmitOutput;
use App\Repository\VoteRepository;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class VoteService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private InteractionService $interactionService,
        private UserService $userService,
        private VoteRepository $voteRepository,
        private VoteFactory $voteFactory
    ) {
    }

    public function vote(
        SubmitInput $input,
        ?UserInterface $user,
        ?RequestDetailsInterface $requestDetails
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

        $this->interactionService->record(InteractionService::TYPE_VOTE, $vote->getId(), $requestDetails, $user);

        switch ($input->getEntityName()) {
            case 'Comment':
                assert($vote instanceof CommentVote);

                return $this->voteFactory->createSubmitOutputFromComment($vote->getComment());
            case 'LocaleReview':
                assert($vote instanceof LocaleReviewVote);

                return $this->voteFactory->createSubmitOutputFromReview($vote->getLocaleReview());
            case 'TenancyReview':
                assert($vote instanceof TenancyReviewVote);

                return $this->voteFactory->createSubmitOutputFromTenancyReview($vote->getTenancyReview());
        }

        return new SubmitOutput(true);
    }

    private function findExisting(SubmitInput $input, User $user): ?Vote
    {
        switch ($input->getEntityName()) {
            case 'Comment':
                return $this->voteRepository->findOneCommentVoteByUserAndEntity($user, $input->getEntityId());
            case 'LocaleReview':
                return $this->voteRepository->findOneLocaleReviewVoteByUserAndEntity($user, $input->getEntityId());
            case 'TenancyReview':
                return $this->voteRepository->findOneTenancyReviewVoteByUserAndEntity($user, $input->getEntityId());
            // @codeCoverageIgnoreStart
            default:
                return null;
        }
        // @codeCoverageIgnoreEnd
    }
}
