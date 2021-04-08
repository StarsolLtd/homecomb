<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Factory\FlashMessageFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SessionController extends AppController
{
    private FlashMessageFactory $flashMessageFactory;

    public function __construct(
        FlashMessageFactory $flashMessageFactory,
        SerializerInterface $serializer
    ) {
        $this->flashMessageFactory = $flashMessageFactory;
        $this->serializer = $serializer;
    }

    /**
     * @Route (
     *     "/api/session/flash",
     *     name="api-session-flash",
     *     methods={"GET"}
     * )
     */
    public function getFlashBag(): JsonResponse
    {
        /** @var Session $session */
        $session = $this->get('session');
        $view = $this->flashMessageFactory->getFlashMessages($session->getFlashBag());

        return $this->jsonResponse($view, Response::HTTP_OK);
    }
}
