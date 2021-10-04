<?php

namespace App\Service;

use App\Entity\Locale\Locale;
use App\Entity\Postcode;
use App\Entity\TenancyReview;
use App\Exception\UnexpectedValueException;
use App\Factory\TenancyReviewFactory;
use App\Model\Interaction\RequestDetails;
use App\Model\TenancyReview\Group;
use App\Model\TenancyReview\SubmitInput;
use App\Model\TenancyReview\SubmitOutput;
use App\Model\TenancyReview\View;
use App\Repository\PostcodeRepository;
use App\Repository\PropertyRepository;
use App\Repository\TenancyReviewRepository;
use App\Service\Agency\FindOrCreateService as AgencyFindOrCreateService;
use App\Service\Branch\BranchFindOrCreateService;
use App\Service\TenancyReviewSolicitation\CompleteService;
use App\Service\User\UserService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TenancyReviewService
{
    public function __construct(
        private AgencyFindOrCreateService $agencyFindOrCreateService,
        private BranchFindOrCreateService $branchFindOrCreateService,
        private InteractionService $interactionService,
        private NotificationService $notificationService,
        private CompleteService $completeService,
        private UserService $userService,
        private EntityManagerInterface $entityManager,
        private PostcodeRepository $postcodeRepository,
        private PropertyRepository $propertyRepository,
        private TenancyReviewRepository $tenancyReviewRepository,
        private TenancyReviewFactory $tenancyReviewFactory
    ) {
    }

    public function submitReview(
        SubmitInput $reviewInput,
        ?UserInterface $user,
        ?RequestDetails $requestDetails = null
    ): SubmitOutput {
        $property = $this->propertyRepository->findOnePublishedBySlug($reviewInput->getPropertySlug());

        $agencyName = $reviewInput->getAgencyName();
        $agency = $agencyName ? $this->agencyFindOrCreateService->findOrCreateByName($agencyName) : null;
        $branchName = $reviewInput->getAgencyBranch();
        $branch = $branchName ? $this->branchFindOrCreateService->findOrCreate($branchName, $agency) : null;
        $userEntity = $this->userService->getUserEntityOrNullFromUserInterface($user);

        $tenancyReview = $this->tenancyReviewFactory->createEntity($reviewInput, $property, $branch, $userEntity);

        $this->entityManager->persist($tenancyReview);

        $code = $reviewInput->getCode();
        if (null !== $code) {
            $this->completeService->complete($code, $tenancyReview);
        }

        $this->entityManager->flush();

        $this->notificationService->sendTenancyReviewModerationNotification($tenancyReview);

        if (null !== $requestDetails) {
            try {
                $this->interactionService->record(
                    'Review',
                    $tenancyReview->getId(),
                    $requestDetails,
                    $user
                );
            } catch (UnexpectedValueException $e) {
                // Shrug shoulders
            }
        }

        return new SubmitOutput(true);
    }

    public function publishReview(TenancyReview $tenancyReview): void
    {
        $tenancyReview->setPublished(true);
        $this->entityManager->flush();
    }

    /**
     * @return Collection<int, Locale>
     */
    public function generateLocales(TenancyReview $tenancyReview): Collection
    {
        /** @var Collection<int, Locale> $locales */
        $locales = new ArrayCollection();

        $fullPostcode = $tenancyReview->getProperty()->getPostcode();
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
            $locale->addTenancyReview($tenancyReview);
        }

        $this->entityManager->flush();

        return $locales;
    }

    public function getViewById(int $tenancyReviewId): View
    {
        $entity = $this->tenancyReviewRepository->findOnePublishedById($tenancyReviewId);

        return $this->tenancyReviewFactory->createViewFromEntity($entity);
    }

    public function getLatestGroup(int $limit = 3): Group
    {
        $tenancyReviews = $this->tenancyReviewRepository->findLatest($limit);

        return $this->tenancyReviewFactory->createGroup('Latest Reviews', $tenancyReviews);
    }
}
