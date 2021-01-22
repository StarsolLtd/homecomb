<?php

namespace App\Service;

use App\Entity\Interaction\AnswerInteraction;
use App\Entity\Interaction\FlagInteraction;
use App\Entity\Interaction\ReviewInteraction;
use App\Exception\UnexpectedValueException;
use App\Model\Interaction\RequestDetails;
use App\Repository\FlagRepository;
use App\Repository\ReviewRepository;
use App\Repository\Survey\AnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use function sprintf;
use Symfony\Component\Security\Core\User\UserInterface;

class InteractionService
{
    private EntityManagerInterface $entityManager;
    private UserService $userService;
    private AnswerRepository $answerRepository;
    private FlagRepository $flagRepository;
    private ReviewRepository $reviewRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserService $userService,
        AnswerRepository $answerRepository,
        FlagRepository $flagRepository,
        ReviewRepository $reviewRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->answerRepository = $answerRepository;
        $this->flagRepository = $flagRepository;
        $this->reviewRepository = $reviewRepository;
    }

    public function record(
        string $entityName,
        int $entityId,
        RequestDetails $requestDetails,
        ?UserInterface $user = null
    ): void {
        switch ($entityName) {
            case 'Answer':
                $answer = $this->answerRepository->findOneById($entityId);
                $interaction = (new AnswerInteraction())->setAnswer($answer);
                break;
            case 'Flag':
                $flag = $this->flagRepository->findOneById($entityId);
                $interaction = (new FlagInteraction())->setFlag($flag);
                break;
            case 'Review':
                $review = $this->reviewRepository->findOneById($entityId);
                $interaction = (new ReviewInteraction())->setReview($review);
                break;
            default:
                throw new UnexpectedValueException(sprintf('%s is not a valid interaction entity name.', $entityName));
        }

        $userEntity = $this->userService->getUserEntityOrNullFromUserInterface($user);

        $interaction
            ->setUser($userEntity)
            ->setSessionId($requestDetails->getSessionId())
            ->setIpAddress($requestDetails->getIpAddress())
            ->setUserAgent($requestDetails->getUserAgent())
        ;

        $this->entityManager->persist($interaction);
        $this->entityManager->flush();
    }
}
