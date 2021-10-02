<?php

namespace App\Service;

use App\Entity\Interaction\AnswerInteraction;
use App\Entity\Interaction\FlagInteraction;
use App\Entity\Interaction\TenancyReviewInteraction;
use App\Exception\UnexpectedValueException;
use App\Model\Interaction\RequestDetails;
use App\Repository\FlagRepository;
use App\Repository\Survey\AnswerRepository;
use App\Repository\TenancyReviewRepository;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class InteractionService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserService $userService,
        private AnswerRepository $answerRepository,
        private FlagRepository $flagRepository,
        private TenancyReviewRepository $tenancyReviewRepository
    ) {
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
            case 'TenancyReview':
                $tenancyReview = $this->tenancyReviewRepository->findOneById($entityId);
                $interaction = (new TenancyReviewInteraction())->setTenancyReview($tenancyReview);
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
