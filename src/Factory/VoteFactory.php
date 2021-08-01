<?php

namespace App\Factory;

use App\Entity\Comment\Comment;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Entity\Vote\CommentVote;
use App\Entity\Vote\TenancyReviewVote;
use App\Entity\Vote\Vote;
use App\Exception\UnexpectedValueException;
use App\Model\Vote\SubmitInput;
use App\Model\Vote\SubmitOutput;
use App\Repository\CommentRepository;
use App\Repository\TenancyReviewRepository;
use function sprintf;

class VoteFactory
{
    private CommentRepository $commentRepository;
    private TenancyReviewRepository $tenancyReviewRepository;

    public function __construct(
        CommentRepository $commentRepository,
        TenancyReviewRepository $tenancyReviewRepository
    ) {
        $this->commentRepository = $commentRepository;
        $this->tenancyReviewRepository = $tenancyReviewRepository;
    }

    public function createEntityFromSubmitInput(SubmitInput $input, User $user): Vote
    {
        $entityName = $input->getEntityName();
        $entityId = $input->getEntityId();

        switch ($entityName) {
            case 'Comment':
                $comment = $this->commentRepository->findOnePublishedById($entityId);
                $vote = (new CommentVote())->setComment($comment);
                break;
            case 'TenancyReview':
                $tenancyReview = $this->tenancyReviewRepository->findOnePublishedById($entityId);
                $vote = (new TenancyReviewVote())->setTenancyReview($tenancyReview);
                break;
            default:
                throw new UnexpectedValueException(sprintf('%s is not a valid Vote entity name.', $entityName));
        }

        $vote
            ->setPositive($input->isPositive())
            ->setUser($user);

        return $vote;
    }

    public function createSubmitOutputFromReview(TenancyReview $tenancyReview): SubmitOutput
    {
        return new SubmitOutput(
            true,
            'Review',
            $tenancyReview->getId(),
            $tenancyReview->getPositiveVotesCount(),
            $tenancyReview->getNegativeVotesCount(),
            $tenancyReview->getVotesScore()
        );
    }

    public function createSubmitOutputFromComment(Comment $comment): SubmitOutput
    {
        return new SubmitOutput(
            true,
            'Comment',
            $comment->getId(),
            $comment->getPositiveVotesCount(),
            $comment->getNegativeVotesCount(),
            $comment->getVotesScore()
        );
    }
}
