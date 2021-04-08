<?php

namespace App\Factory;

use App\Entity\Comment\Comment;
use App\Entity\Comment\ReviewComment;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Model\Comment\SubmitInput;
use App\Repository\ReviewRepository;
use function sprintf;

class CommentFactory
{
    private ReviewRepository $reviewRepository;

    public function __construct(
        ReviewRepository $reviewRepository
    ) {
        $this->reviewRepository = $reviewRepository;
    }

    public function createEntityFromSubmitInput(SubmitInput $input, User $user): Comment
    {
        $entityName = $input->getEntityName();

        switch ($input->getEntityName()) {
            case 'Review':
                $review = $this->reviewRepository->findOnePublishedById($input->getEntityId());
                $comment = new ReviewComment();
                $review->addComment($comment);
                break;
            default:
                throw new UnexpectedValueException(sprintf('%s is not a valid comment related entity name.', $entityName));
        }

        $comment
            ->setRelatedEntityId($input->getEntityId())
            ->setContent($input->getContent())
            ->setUser($user);

        return $comment;
    }
}
