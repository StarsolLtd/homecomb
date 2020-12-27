<?php

namespace App\Factory;

use App\Entity\ReviewSolicitation;
use App\Entity\User;
use App\Exception\DeveloperException;
use App\Model\ReviewSolicitation\CreateReviewSolicitationInput;
use App\Model\ReviewSolicitation\FormData;
use App\Repository\BranchRepository;
use App\Repository\PropertyRepository;
use function sha1;

class ReviewSolicitationFactory
{
    private BranchRepository $branchRepository;
    private PropertyRepository $propertyRepository;
    private FlatModelFactory $flatModelFactory;

    public function __construct(
        BranchRepository $branchRepository,
        PropertyRepository $propertyRepository,
        FlatModelFactory $flatModelFactory
    ) {
        $this->branchRepository = $branchRepository;
        $this->propertyRepository = $propertyRepository;
        $this->flatModelFactory = $flatModelFactory;
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

    public function createFormDataModelFromUser(User $user): FormData
    {
        $agencyEntity = $user->getAdminAgency();
        if (null === $agencyEntity) {
            throw new DeveloperException('User is not an agency admin.');
        }
        $agency = $this->flatModelFactory->getAgencyFlatModel($agencyEntity);

        $branches = [];
        foreach ($agencyEntity->getBranches() as $branchEntity) {
            $branches[] = $this->flatModelFactory->getBranchFlatModel($branchEntity);
        }

        return new FormData(
            $agency,
            $branches
        );
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
