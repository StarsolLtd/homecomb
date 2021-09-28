<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Factory\FlashMessageFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class SessionController extends AppController
{
    public function __construct(
        private FlashMessageFactory $flashMessageFactory,
        protected SerializerInterface $serializer,
    ) {
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
