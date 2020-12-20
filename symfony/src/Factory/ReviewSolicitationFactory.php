<?php

namespace App\Factory;

use App\Entity\ReviewSolicitation;
use App\Entity\User;
use App\Model\ReviewSolicitation\CreateReviewSolicitationInput;
use App\Repository\BranchRepository;
use App\Repository\PropertyRepository;
use function sha1;

class ReviewSolicitationFactory
{
    private BranchRepository $branchRepository;
    private PropertyRepository $propertyRepository;

    public function __construct(
        BranchRepository $branchRepository,
        PropertyRepository $propertyRepository
    ) {
        $this->branchRepository = $branchRepository;
        $this->propertyRepository = $propertyRepository;
    }

    public function createEntityFromInput(
        CreateReviewSolicitationInput $input,
        User $senderUser
    ): ReviewSolicitation {
        $branch = $this->branchRepository->findOnePublishedBySlug($input->getBranchSlug());
        $property = $this->propertyRepository->findOnePublishedBySlug($input->getPropertySlug());

        $reviewSolicitation = (new ReviewSolicitation())
            ->setBranch($branch)
            ->setSenderUser($senderUser)
            ->setProperty($property)
            ->setRecipientTitle($input->getRecipientTitle())
            ->setRecipientFirstName($input->getRecipientFirstName())
            ->setRecipientLastName($input->getRecipientLastName())
            ->setRecipientEmail($input->getRecipientEmail())
        ;

        $reviewSolicitation->setCode($this->generateCode($reviewSolicitation));

        return $reviewSolicitation;
    }

    private function generateCode(ReviewSolicitation $reviewSolicitation): string
    {
        return sha1(
            $reviewSolicitation->getBranch()->getSlug()
            .'/'
            .$reviewSolicitation->getProperty()->getSlug()
            .'/'
            .$reviewSolicitation->getRecipientEmail()
            .'/nE5rB7dW8nF3yG0p'
        );
    }
}
