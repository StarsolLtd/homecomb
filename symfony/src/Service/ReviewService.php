<?php

namespace App\Service;

use App\Entity\Review;
use App\Model\SubmitReviewInput;
use App\Model\SubmitReviewOutput;
use App\Repository\PropertyRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ReviewService
{
    private EntityManagerInterface $entityManager;
    private PropertyRepository $propertyRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        PropertyRepository $propertyRepository
    ) {
        $this->entityManager = $entityManager;
        $this->propertyRepository = $propertyRepository;
    }

    public function submitReview(SubmitReviewInput $reviewInput): SubmitReviewOutput
    {
        $propertyId = $reviewInput->getPropertyId();

        $property = $this->propertyRepository->find($propertyId);

        if (null === $property) {
            throw new \RuntimeException('Property with ID '.$propertyId.' not found.');
        }

        // TODO find or create agency, branch and user

        $review = (new Review())
            ->setProperty($property)
            ->setAuthor($reviewInput->getReviewerName())
            ->setTitle($reviewInput->getReviewTitle())
            ->setContent($reviewInput->getReviewContent())
            ->setOverallStars($reviewInput->getOverallStars())
            ->setAgencyStars($reviewInput->getAgencyStars())
            ->setLandlordStars($reviewInput->getLandlordStars())
            ->setPropertyStars($reviewInput->getPropertyStars())
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());

        $this->entityManager->persist($review);
        $this->entityManager->flush();

        return new SubmitReviewOutput($review->getId());
    }
}
