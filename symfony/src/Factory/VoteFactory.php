<?php

namespace App\Factory;

use App\Entity\User;
use App\Entity\Vote\CommentVote;
use App\Entity\Vote\ReviewVote;
use App\Entity\Vote\Vote;
use App\Exception\UnexpectedValueException;
use App\Model\Vote\SubmitInput;
use App\Repository\CommentRepository;
use App\Repository\ReviewRepository;
use function sprintf;

class VoteFactory
{
    private CommentRepository $commentRepository;
    private ReviewRepository $reviewRepository;

    public function __construct(
        CommentRepository $commentRepository,
        ReviewRepository $reviewRepository
    ) {
        $this->commentRepository = $commentRepository;
        $this->reviewRepository = $reviewRepository;
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
            case 'Review':
                $review = $this->reviewRepository->findOnePublishedById($entityId);
                $vote = (new ReviewVote())->setReview($review);
                break;
            default:
                throw new UnexpectedValueException(sprintf('%s is not a valid Vote entity name.', $entityName));
        }

        $vote
            ->setPositive($input->isPositive())
            ->setUser($user);

        return $vote;
    }
}
