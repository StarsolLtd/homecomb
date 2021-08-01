<?php

namespace App\Factory;

use App\Entity\Comment\Comment;
use App\Entity\Comment\TenancyReviewComment;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Model\Comment\SubmitInput;
use App\Repository\TenancyReviewRepository;
use function sprintf;

class CommentFactory
{
    private TenancyReviewRepository $tenancyReviewRepository;

    public function __construct(
        TenancyReviewRepository $tenancyReviewRepository
    ) {
        $this->tenancyReviewRepository = $tenancyReviewRepository;
    }

    public function createEntityFromSubmitInput(SubmitInput $input, User $user): Comment
    {
        $entityName = $input->getEntityName();

        switch ($input->getEntityName()) {
            case 'Review':
                $tenancyReview = $this->tenancyReviewRepository->findOnePublishedById($input->getEntityId());
                $comment = new TenancyReviewComment();
                $tenancyReview->addComment($comment);
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
