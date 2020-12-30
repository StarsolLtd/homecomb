<?php

namespace App\Factory;

use App\Entity\ReviewSolicitation;
use App\Entity\User;
use App\Exception\DeveloperException;
use App\Exception\NotFoundException;
use App\Model\ReviewSolicitation\CreateReviewSolicitationInput;
use App\Model\ReviewSolicitation\FormData;
use App\Model\ReviewSolicitation\View;
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
        foreach ($agencyEntity->getPublishedBranches() as $branchEntity) {
            $branches[] = $this->flatModelFactory->getBranchFlatModel($branchEntity);
        }

        return new FormData(
            $agency,
            $branches
        );
    }

    public function createViewByEntity(ReviewSolicitation $entity): View
    {
        $agencyEntity = $entity->getBranch()->getAgency();
        if (null === $agencyEntity) {
            throw new NotFoundException(sprintf('Agency not found for ReviewSolicitation %s', $entity->getId()));
        }

        $agency = $this->flatModelFactory->getAgencyFlatModel($agencyEntity);
        $branch = $this->flatModelFactory->getBranchFlatModel($entity->getBranch());
        $property = $this->flatModelFactory->getPropertyFlatModel($entity->getProperty());

        return new View(
            $entity->getCode(),
            $agency,
            $branch,
            $property,
            $entity->getRecipientTitle(),
            $entity->getRecipientFirstName(),
            $entity->getRecipientLastName(),
            $entity->getRecipientEmail(),
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
