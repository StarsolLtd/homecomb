<?php

namespace App\Service;

use App\Entity\Flag\AgencyFlag;
use App\Entity\Flag\BranchFlag;
use App\Entity\Flag\PropertyFlag;
use App\Entity\Flag\ReviewFlag;
use App\Exception\UnexpectedValueException;
use App\Model\Flag\SubmitInput;
use App\Model\Flag\SubmitOutput;
use App\Repository\AgencyRepository;
use App\Repository\BranchRepository;
use App\Repository\PropertyRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use function sprintf;
use Symfony\Component\Security\Core\User\UserInterface;

class FlagService
{
    private EntityManagerInterface $entityManager;
    private NotificationService $notificationService;
    private UserService $userService;
    private AgencyRepository $agencyRepository;
    private BranchRepository $branchRepository;
    private PropertyRepository $propertyRepository;
    private ReviewRepository $reviewRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationService $notificationService,
        UserService $userService,
        AgencyRepository $agencyRepository,
        BranchRepository $branchRepository,
        PropertyRepository $propertyRepository,
        ReviewRepository $reviewRepository
    ) {
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;
        $this->userService = $userService;
        $this->agencyRepository = $agencyRepository;
        $this->branchRepository = $branchRepository;
        $this->propertyRepository = $propertyRepository;
        $this->reviewRepository = $reviewRepository;
    }

    public function submitFlag(SubmitInput $submitInput, ?UserInterface $user): SubmitOutput
    {
        $entityName = $submitInput->getEntityName();
        $entityId = $submitInput->getEntityId();

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

        $userEntity = $this->userService->getUserEntityOrNullFromUserInterface($user);

        $flag->setContent($submitInput->getContent())
            ->setUser($userEntity);

        $this->entityManager->persist($flag);
        $this->entityManager->flush();

        $this->notificationService->sendFlagModerationNotification($flag);

        return new SubmitOutput(true);
    }
}
