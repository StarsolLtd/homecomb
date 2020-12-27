<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Service\ReviewSolicitationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ReviewSolicitationController extends AppController
{
    private ReviewSolicitationService $reviewSolicitationService;

    public function __construct(
        ReviewSolicitationService $reviewSolicitationService,
        SerializerInterface $serializer
    ) {
        $this->reviewSolicitationService = $reviewSolicitationService;
        $this->serializer = $serializer;
    }

    /**
     * @Route (
     *     "/api/rs/{code}",
     *     name="api-review-solicitation-view",
     *     methods={"GET"}
     * )
     */
    public function view(string $code): JsonResponse
    {
        $view = $this->reviewSolicitationService->getViewByCode($code);

        return $this->jsonResponse($view, Response::HTTP_OK);
    }
}
