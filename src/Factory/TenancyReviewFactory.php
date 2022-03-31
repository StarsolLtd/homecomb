<?php

namespace App\Factory;

use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Model\TenancyReview\Group;
use App\Model\TenancyReview\Stars;
use App\Model\TenancyReview\SubmitInputInterface;
use App\Model\TenancyReview\View;
use App\Util\ReviewHelper;
use DateTime;

class TenancyReviewFactory
{
    public function __construct(
        private FlatModelFactory $flatModelFactory,
        private ReviewHelper $reviewHelper
    ) {
    }

    public function createEntity(
        SubmitInputInterface $input,
        Property $property,
        ?Branch $branch,
        ?User $user
    ): TenancyReview {
        $tenancyReview = (new TenancyReview())
            ->setProperty($property)
            ->setBranch($branch)
            ->setAuthor($input->getReviewerName())
            ->setTitle($input->getReviewTitle())
            ->setContent($input->getReviewContent())
            ->setOverallStars($input->getOverallStars())
            ->setAgencyStars($input->getAgencyStars())
            ->setLandlordStars($input->getLandlordStars())
            ->setPropertyStars($input->getPropertyStars())
            ->setUser($user)
        ;

        $start = $input->getStart();
        if (null !== $start) {
            $tenancyReview->setStart(new DateTime($start));
        }
        $end = $input->getEnd();
        if (null !== $end) {
            $tenancyReview->setEnd(new DateTime($end));
        }

        $tenancyReview->setSlug($this->reviewHelper->generateTenancyReviewSlug($tenancyReview));

        return $tenancyReview;
    }

    public function createViewFromEntity(TenancyReview $entity): View
    {
        $agency = null;
        $agencyEntity = $entity->getAgency();
        if (null !== $agencyEntity) {
            $agency = $this->flatModelFactory->getAgencyFlatModel($agencyEntity);
        }

        $branch = null;
        $branchEntity = $entity->getBranch();
        if (null !== $branchEntity) {
            $branch = $this->flatModelFactory->getBranchFlatModel($branchEntity);
        }

        $property = null;
        $propertyEntity = $entity->getProperty();
        if (null !== $propertyEntity) {
            $property = $this->flatModelFactory->getPropertyFlatModel($propertyEntity);
        }

        $stars = $this->createStarsFromEntity($entity);

        $comments = [];
        foreach ($entity->getPublishedComments() as $comment) {
            $comments[] = $this->flatModelFactory->getCommentFlatModel($comment);
        }

        return new View(
            $branch,
            $agency,
            $property,
            $entity->getId() ?? 0,
            $entity->getAuthor() ?? '',
            $entity->getStart(),
            $entity->getEnd(),
            $entity->getTitle() ?? '',
            $entity->getContent() ?? '',
            $stars,
            $entity->getCreatedAt(),
            $comments,
            $entity->getPositiveVotesCount(),
            $entity->getNegativeVotesCount(),
            $entity->getVotesScore()
        );
    }

    /**
     * @param TenancyReview[] $tenancyReviewEntities
     */
    public function createGroup(string $title, array $tenancyReviewEntities): Group
    {
        $tenancyReviews = [];
        foreach ($tenancyReviewEntities as $tenancyReviewEntity) {
            $tenancyReviews[] = $this->createViewFromEntity($tenancyReviewEntity);
        }

        return new Group(
            $title,
            $tenancyReviews
        );
    }

    private function createStarsFromEntity(TenancyReview $entity): Stars
    {
        return new Stars(
            $entity->getOverallStars(),
            $entity->getPropertyStars(),
            $entity->getAgencyStars(),
            $entity->getLandlordStars(),
        );
    }
}
