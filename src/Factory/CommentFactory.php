<?php

namespace App\Factory;

use App\Entity\Comment\Comment;
use App\Entity\Comment\TenancyReviewComment;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Model\Comment\SubmitInputInterface;
use App\Repository\TenancyReviewRepositoryInterface;

class CommentFactory
{
    public function __construct(
        private TenancyReviewRepositoryInterface $tenancyReviewRepository,
    ) {
    }

    public function createEntityFromSubmitInput(SubmitInputInterface $input, User $user): Comment
    {
        $entityName = $input->getEntityName();
        $entityId = $input->getEntityId();

        switch ($entityName) {
            case 'Review':
                $tenancyReview = $this->tenancyReviewRepository->findOnePublishedById($entityId);
                $comment = new TenancyReviewComment();
                $tenancyReview->addComment($comment);
                break;
            default:
                throw new UnexpectedValueException(sprintf('%s is not a valid comment related entity name.', $entityName));
        }

        $comment
            ->setRelatedEntityId($entityId)
            ->setContent($input->getContent())
            ->setUser($user);

        return $comment;
    }
}
