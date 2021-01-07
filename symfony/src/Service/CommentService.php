<?php

namespace App\Service;

use App\Factory\CommentFactory;
use App\Model\Comment\SubmitInput;
use App\Model\Comment\SubmitOutput;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentService
{
    private EntityManagerInterface $entityManager;
    private CommentFactory $commentFactory;
    private UserService $userService;

    public function __construct(
        EntityManagerInterface $entityManager,
        CommentFactory $commentFactory,
        UserService $userService
    ) {
        $this->entityManager = $entityManager;
        $this->commentFactory = $commentFactory;
        $this->userService = $userService;
    }

    public function submitComment(SubmitInput $submitInput, ?UserInterface $user): SubmitOutput
    {
        $userEntity = $this->userService->getEntityFromInterface($user);

        $comment = $this->commentFactory->createEntityFromSubmitInput($submitInput, $userEntity);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return new SubmitOutput(true);
    }
}
