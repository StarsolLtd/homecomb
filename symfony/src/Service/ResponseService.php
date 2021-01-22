<?php

namespace App\Service;

use App\Entity\Survey\Response;
use App\Entity\Survey\Survey;
use App\Factory\Survey\ResponseFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ResponseService
{
    private EntityManagerInterface $entityManager;
    private SessionService $sessionService;
    private UserService $userService;
    private ResponseFactory $responseFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        SessionService $sessionService,
        UserService $userService,
        ResponseFactory $responseFactory
    ) {
        $this->entityManager = $entityManager;
        $this->sessionService = $sessionService;
        $this->userService = $userService;
        $this->responseFactory = $responseFactory;
    }

    public function create(Survey $survey, ?UserInterface $user): Response
    {
        $user = $this->userService->getUserEntityOrNullFromUserInterface($user);

        $response = $this->responseFactory->createEntity($survey, $user);

        $this->entityManager->persist($response);
        $this->entityManager->flush();

        return $response;
    }
}
