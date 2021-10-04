<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\NotFoundException;
use App\Service\Agency\ViewService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class AgencyController extends AppController
{
    public function __construct(
        private ViewService $getViewService,
        protected SerializerInterface $serializer,
    ) {
    }

    /**
     * @Route (
     *     "/api/agency/{slug}",
     *     name="api-agency-view",
     *     methods={"GET"}
     * )
     */
    public function view(string $slug): JsonResponse
    {
        try {
            $view = $this->getViewService->getViewBySlug($slug);
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($view, Response::HTTP_OK);
    }
}
