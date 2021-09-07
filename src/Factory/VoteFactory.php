<?php

namespace App\Factory;

use App\Entity\Comment\Comment;
use App\Entity\Review\LocaleReview;
use App\Entity\Review\Review;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Entity\Vote\CommentVote;
use App\Entity\Vote\LocaleReviewVote;
use App\Entity\Vote\TenancyReviewVote;
use App\Entity\Vote\Vote;
use App\Exception\UnexpectedValueException;
use App\Model\Vote\SubmitInput;
use App\Model\Vote\SubmitOutput;
use App\Repository\CommentRepository;
use App\Repository\ReviewRepository;
use App\Repository\TenancyReviewRepository;
use function sprintf;

class VoteFactory
{
    public function __construct(
        private CommentRepository $commentRepository,
        private ReviewRepository $reviewRepository,
        private TenancyReviewRepository $tenancyReviewRepository
    ) {
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
            case 'LocaleReview':
                $review = $this->reviewRepository->findOnePublishedById($entityId);
                assert($review instanceof LocaleReview);
                $vote = (new LocaleReviewVote())->setLocaleReview($review);
                break;
            case 'TenancyReview':
                $review = $this->tenancyReviewRepository->findOnePublishedById($entityId);
                $vote = (new TenancyReviewVote())->setTenancyReview($review);
                break;
            default:
                throw new UnexpectedValueException(sprintf('%s is not a valid Vote entity name.', $entityName));
        }

        $vote
            ->setPositive($input->isPositive())
            ->setUser($user);

        return $vote;
    }

    public function createSubmitOutputFromReview(Review $review): SubmitOutput
    {
        return new SubmitOutput(
            true,
            'Review',
            $review->getId(),
            $review->getPositiveVotesCount(),
            $review->getNegativeVotesCount(),
            $review->getVotesScore()
        );
    }

    public function createSubmitOutputFromTenancyReview(TenancyReview $tenancyReview): SubmitOutput
    {
        return new SubmitOutput(
            true,
            'TenancyReview',
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
