<?php

namespace App\Service;

use App\Entity\Interaction\AnswerInteraction;
use App\Entity\Interaction\FlagInteraction;
use App\Entity\Interaction\Interaction;
use App\Entity\Interaction\TenancyReviewInteraction;
use App\Model\Interaction\RequestDetails;
use App\Repository\FlagRepository;
use App\Repository\Survey\AnswerRepository;
use App\Repository\TenancyReviewRepository;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class InteractionService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private UserService $userService,
        private AnswerRepository $answerRepository,
        private FlagRepository $flagRepository,
        private TenancyReviewRepository $tenancyReviewRepository
    ) {
    }

    public function record(
        string $entityName,
        int $entityId,
        ?RequestDetails $requestDetails,
        ?UserInterface $user = null
    ): void {
        if (null === $requestDetails) {
            return;
        }

        $interaction = $this->createInteraction($entityName, $entityId);
        if (!$interaction) {
            return;
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

    private function createInteraction(string $entityName, int $entityId): ?Interaction
    {
        switch ($entityName) {
            case 'Answer':
                $answer = $this->answerRepository->findOneById($entityId);

                return (new AnswerInteraction())->setAnswer($answer);
            case 'Flag':
                $flag = $this->flagRepository->findOneById($entityId);

                return (new FlagInteraction())->setFlag($flag);
            case 'TenancyReview':
                $tenancyReview = $this->tenancyReviewRepository->findOneById($entityId);

                return (new TenancyReviewInteraction())->setTenancyReview($tenancyReview);
            default:
                $this->logger->warning(sprintf('%s is not a valid interaction entity name.', $entityName));

                return null;
        }
    }
}
