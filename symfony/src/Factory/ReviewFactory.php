<?php

namespace App\Factory;

use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\Review;
use App\Entity\User;
use App\Model\Review\Group;
use App\Model\Review\Stars;
use App\Model\Review\View;
use App\Model\SubmitReviewInput;

class ReviewFactory
{
    private FlatModelFactory $flatModelFactory;

    public function __construct(
        FlatModelFactory $flatModelFactory
    ) {
        $this->flatModelFactory = $flatModelFactory;
    }

    public function createEntity(
        SubmitReviewInput $input,
        Property $property,
        ?Branch $branch,
        ?User $user
    ): Review {
        return (new Review())
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
    }

    public function createViewFromEntity(Review $entity): View
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
            $entity->getTitle() ?? '',
            $entity->getContent() ?? '',
            $stars,
            $entity->getCreatedAt(),
            $comments
        );
    }

    /**
     * @param Review[] $reviewEntities
     */
    public function createGroup(string $title, array $reviewEntities): Group
    {
        $reviews = [];
        foreach ($reviewEntities as $reviewEntity) {
            $reviews[] = $this->createViewFromEntity($reviewEntity);
        }

        return new Group(
            $title,
            $reviews
        );
    }

    private function createStarsFromEntity(Review $entity): Stars
    {
        return new Stars(
            $entity->getOverallStars(),
            $entity->getPropertyStars(),
            $entity->getAgencyStars(),
            $entity->getLandlordStars(),
        );
    }
}
