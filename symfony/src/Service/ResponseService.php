<?php

namespace App\Service;

use App\Factory\Survey\ResponseFactory;
use App\Model\Survey\CreateResponseInput;
use App\Model\Survey\CreateResponseOutput;
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

    public function create(CreateResponseInput $input, ?UserInterface $user): CreateResponseOutput
    {
        $user = $this->userService->getUserEntityOrNullFromUserInterface($user);

        $response = $this->responseFactory->createEntityFromCreateInput($input, $user);

        $this->entityManager->persist($response);
        $this->entityManager->flush();

        $this->sessionService->set('survey_'.$response->getSurvey()->getId().'_response_id', $response->getId());

        return new CreateResponseOutput(true);
    }
}
