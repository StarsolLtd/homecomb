<?php

namespace App\Service;

use App\Exception\UnexpectedValueException;
use App\Factory\CommentFactory;
use App\Model\Comment\SubmitInput;
use App\Model\Comment\SubmitOutput;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private CommentFactory $commentFactory,
        private UserService $userService
    ) {
    }

    public function submitComment(SubmitInput $submitInput, ?UserInterface $user): SubmitOutput
    {
        $userEntity = $this->userService->getEntityFromInterface($user);

        try {
            $comment = $this->commentFactory->createEntityFromSubmitInput($submitInput, $userEntity);
        } catch (UnexpectedValueException $e) {
            $this->logger->warning($e->getMessage());
            throw $e;
        }

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return new SubmitOutput(true);
    }
}
