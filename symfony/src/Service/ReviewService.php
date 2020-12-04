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
    private AgencyService $agencyService;
    private BranchService $branchService;
    private EntityManagerInterface $entityManager;
    private PropertyRepository $propertyRepository;

    public function __construct(
        AgencyService $agencyService,
        BranchService $branchService,
        EntityManagerInterface $entityManager,
        PropertyRepository $propertyRepository
    ) {
        $this->agencyService = $agencyService;
        $this->branchService = $branchService;
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

        // TODO find or create user

        $agencyName = $reviewInput->getAgencyName();
        $agency = $agencyName ? $this->agencyService->findOrCreateByName($agencyName) : null;
        $branchName = $reviewInput->getAgencyBranch();
        $branch = $branchName ? $this->branchService->findOrCreate($branchName, $agency) : null;

        $review = (new Review())
            ->setProperty($property)
            ->setBranch($branch)
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

        return new SubmitReviewOutput(true);
    }

    public function publishReview(Review $review): void
    {
        $review->setPublished(true);
        $this->entityManager->flush();
    }
}
