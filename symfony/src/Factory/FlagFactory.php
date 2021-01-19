<?php

namespace App\Factory;

use App\Entity\Flag\AgencyFlag;
use App\Entity\Flag\BranchFlag;
use App\Entity\Flag\Flag;
use App\Entity\Flag\PropertyFlag;
use App\Entity\Flag\ReviewFlag;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Model\Flag\SubmitInput;
use App\Repository\AgencyRepository;
use App\Repository\BranchRepository;
use App\Repository\PropertyRepository;
use App\Repository\ReviewRepository;
use function sprintf;

class FlagFactory
{
    private AgencyRepository $agencyRepository;
    private BranchRepository $branchRepository;
    private PropertyRepository $propertyRepository;
    private ReviewRepository $reviewRepository;

    public function __construct(
        AgencyRepository $agencyRepository,
        BranchRepository $branchRepository,
        PropertyRepository $propertyRepository,
        ReviewRepository $reviewRepository
    ) {
        $this->agencyRepository = $agencyRepository;
        $this->branchRepository = $branchRepository;
        $this->propertyRepository = $propertyRepository;
        $this->reviewRepository = $reviewRepository;
    }

    public function createEntityFromSubmitInput(SubmitInput $input, ?User $user): Flag
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
                $review = $this->reviewRepository->findOnePublishedById($entityId);
                $flag = (new ReviewFlag())->setReview($review);
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
