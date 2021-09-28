<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class UserController extends AppController
{
    public function __construct(
        private UserService $userService,
        protected SerializerInterface $serializer
    ) {
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

        return $this->jsonResponse($view, Response::HTTP_OK);
    }
}
