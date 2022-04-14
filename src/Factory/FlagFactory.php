<?php

namespace App\Factory;

use App\Entity\Flag\AgencyFlag;
use App\Entity\Flag\BranchFlag;
use App\Entity\Flag\Flag;
use App\Entity\Flag\PropertyFlag;
use App\Entity\Flag\TenancyReviewFlag;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Model\Flag\SubmitInputInterface;
use App\Repository\AgencyRepositoryInterface;
use App\Repository\BranchRepository;
use App\Repository\PropertyRepository;
use App\Repository\TenancyReviewRepository;
use function sprintf;

class FlagFactory
{
    public function __construct(
        private AgencyRepositoryInterface $agencyRepository,
        private BranchRepository $branchRepository,
        private PropertyRepository $propertyRepository,
        private TenancyReviewRepository $tenancyReviewRepository
    ) {
    }

    public function createEntityFromSubmitInput(SubmitInputInterface $input, ?User $user): Flag
    {
        $entityName = $input->getEntityName();
        $entityId = $input->getEntityId();

        switch ($entityName) {
            case 'Agency':
                $agency = $this->agencyRepository->findOnePublishedById($entityId);
                $flag = (new AgencyFlag())->setAgency($agency);
                break;
            case 'Branch':
                $branch = $this->branchRepository->findOnePublishedById($entityId);
                $flag = (new BranchFlag())->setBranch($branch);
                break;
            case 'Property':
                $property = $this->propertyRepository->findOnePublishedById($entityId);
                $flag = (new PropertyFlag())->setProperty($property);
                break;
            case 'Review':
                $tenancyReview = $this->tenancyReviewRepository->findOnePublishedById($entityId);
                $flag = (new TenancyReviewFlag())->setTenancyReview($tenancyReview);
                break;
            default:
                throw new UnexpectedValueException(sprintf('%s is not a valid flag entity name.', $entityName));
        }

        $flag
            ->setContent($input->getContent())
            ->setUser($user);

        return $flag;
    }
}
