<?php

namespace App\Factory;

use App\Entity\TenancyReviewSolicitation;
use App\Entity\User;
use App\Exception\DeveloperException;
use App\Exception\NotFoundException;
use App\Model\TenancyReviewSolicitation\CreateInputInterface;
use App\Model\TenancyReviewSolicitation\FormData;
use App\Model\TenancyReviewSolicitation\View;
use App\Repository\BranchRepositoryInterface;
use App\Repository\PropertyRepositoryInterface;
use function sha1;

class TenancyReviewSolicitationFactory
{
    public function __construct(
        private BranchRepositoryInterface $branchRepository,
        private PropertyRepositoryInterface $propertyRepository,
        private FlatModelFactory $flatModelFactory,
    ) {
    }

    public function createEntityFromInput(
        CreateInputInterface $input,
        User $senderUser
    ): TenancyReviewSolicitation {
        $branch = $this->branchRepository->findOnePublishedBySlug($input->getBranchSlug());
        $property = $this->propertyRepository->findOnePublishedBySlug($input->getPropertySlug());

        $tenancyReviewSolicitation = (new TenancyReviewSolicitation())
            ->setBranch($branch)
            ->setSenderUser($senderUser)
            ->setProperty($property)
            ->setRecipientTitle($input->getRecipientTitle())
            ->setRecipientFirstName($input->getRecipientFirstName())
            ->setRecipientLastName($input->getRecipientLastName())
            ->setRecipientEmail($input->getRecipientEmail())
        ;

        $tenancyReviewSolicitation->setCode($this->generateCode($tenancyReviewSolicitation));

        return $tenancyReviewSolicitation;
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

    public function createViewByEntity(TenancyReviewSolicitation $entity): View
    {
        $agencyEntity = $entity->getBranch()->getAgency();
        if (null === $agencyEntity) {
            throw new NotFoundException(sprintf('Agency not found for TenancyReviewSolicitation %s', $entity->getId()));
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

    private function generateCode(TenancyReviewSolicitation $tenancyReviewSolicitation): string
    {
        return sha1(
            $tenancyReviewSolicitation->getBranch()->getSlug()
            .'/'
            .$tenancyReviewSolicitation->getProperty()->getSlug()
            .'/'
            .$tenancyReviewSolicitation->getRecipientEmail()
            .'/nE5rB7dW8nF3yG0p'
        );
    }
}
