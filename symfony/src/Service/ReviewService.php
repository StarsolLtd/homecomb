<?php

namespace App\Service;

use App\Entity\Locale;
use App\Entity\Postcode;
use App\Entity\Review;
use App\Factory\ReviewFactory;
use App\Model\Interaction\RequestDetails;
use App\Model\Review\Group;
use App\Model\Review\View;
use App\Model\SubmitReviewInput;
use App\Model\SubmitReviewOutput;
use App\Repository\PostcodeRepository;
use App\Repository\PropertyRepository;
use App\Repository\ReviewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

class ReviewService
{
    private AgencyService $agencyService;
    private BranchService $branchService;
    private InteractionService $interactionService;
    private NotificationService $notificationService;
    private ReviewSolicitationService $reviewSolicitationService;
    private UserService $userService;
    private EntityManagerInterface $entityManager;
    private PostcodeRepository $postcodeRepository;
    private PropertyRepository $propertyRepository;
    private ReviewRepository $reviewRepository;
    private ReviewFactory $reviewFactory;

    public function __construct(
        AgencyService $agencyService,
        BranchService $branchService,
        InteractionService $interactionService,
        NotificationService $notificationService,
        ReviewSolicitationService $reviewSolicitationService,
        UserService $userService,
        EntityManagerInterface $entityManager,
        PostcodeRepository $postcodeRepository,
        PropertyRepository $propertyRepository,
        ReviewRepository $reviewRepository,
        ReviewFactory $reviewFactory
    ) {
        $this->agencyService = $agencyService;
        $this->branchService = $branchService;
        $this->interactionService = $interactionService;
        $this->notificationService = $notificationService;
        $this->reviewSolicitationService = $reviewSolicitationService;
        $this->userService = $userService;
        $this->entityManager = $entityManager;
        $this->postcodeRepository = $postcodeRepository;
        $this->propertyRepository = $propertyRepository;
        $this->reviewRepository = $reviewRepository;
        $this->reviewFactory = $reviewFactory;
    }

    public function submitReview(
        SubmitReviewInput $reviewInput,
        ?UserInterface $user,
        ?RequestDetails $requestDetails = null
    ): SubmitReviewOutput {
        $property = $this->propertyRepository->findOnePublishedBySlug($reviewInput->getPropertySlug());

        $agencyName = $reviewInput->getAgencyName();
        $agency = $agencyName ? $this->agencyService->findOrCreateByName($agencyName) : null;
        $branchName = $reviewInput->getAgencyBranch();
        $branch = $branchName ? $this->branchService->findOrCreate($branchName, $agency) : null;
        $userEntity = $this->userService->getUserEntityOrNullFromUserInterface($user);

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
            ->setUser($userEntity);

        $this->entityManager->persist($review);

        $code = $reviewInput->getCode();
        if (null !== $code) {
            $this->reviewSolicitationService->complete($code, $review);
        }

        $this->entityManager->flush();

        $this->notificationService->sendReviewModerationNotification($review);

        if (null !== $requestDetails) {
            try {
                $this->interactionService->record(
                    'Review',
                    $review->getId(),
                    $requestDetails,
                    $user
                );
            } catch (Exception $e) {
                // Shrug shoulders
            }
        }

        return new SubmitReviewOutput(true);
    }

    public function publishReview(Review $review): void
    {
        $review->setPublished(true);
        $this->entityManager->flush();
    }

    /**
     * @return Collection<int, Locale>
     */
    public function generateLocales(Review $review): Collection
    {
        /** @var Collection<int, Locale> $locales */
        $locales = new ArrayCollection();

        $fullPostcode = $review->getProperty()->getPostcode();
        if (!$fullPostcode) {
            return $locales;
        }

        list($first, $second) = explode(' ', $fullPostcode);

        $findBeginningWithString = [$first];
        // TODO also find beginning with parts of $second. For example, "CB4 3", "CB4 3L" and "CB4 3LF".

        /** @var Collection<int, Postcode> $postcodes */
        $postcodes = new ArrayCollection();
        foreach ($findBeginningWithString as $string) {
            foreach ($this->postcodeRepository->findBeginningWith($string) as $postcode) {
                if (!$postcodes->contains($postcode)) {
                    $postcodes->add($postcode);
                }
            }
        }

        /** @var Postcode $postcode */
        foreach ($postcodes as $postcode) {
            foreach ($postcode->getLocales() as $locale) {
                if (!$locales->contains($locale)) {
                    $locales->add($locale);
                }
            }
        }

        foreach ($locales as $locale) {
            $locale->addReview($review);
        }

        $this->entityManager->flush();

        return $locales;
    }

    public function getViewById(int $reviewId): View
    {
        $entity = $this->reviewRepository->findOnePublishedById($reviewId);

        return $this->reviewFactory->createViewFromEntity($entity);
    }

    public function getLatestGroup(int $limit = 3): Group
    {
        $reviews = $this->reviewRepository->findLatest($limit);

        return $this->reviewFactory->createGroup('Latest Reviews', $reviews);
    }
}
