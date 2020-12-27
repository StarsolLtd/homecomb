<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AppController
{
    private UserService $userService;
    private SerializerInterface $serializer;

    public function __construct(
        UserService $userService,
        SerializerInterface $serializer
    ) {
        $this->userService = $userService;
        $this->serializer = $serializer;
    }

    /**
     * @Route (
     *     "/api/user",
     *     name="api-user",
     *     methods={"GET"}
     * )
     */
    public function getUserFlatModel(): JsonResponse
    {
        $view = $this->userService->getFlatModelFromUserInterface($this->getUserInterface());

        return new JsonResponse(
            $this->serializer->serialize($view, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
