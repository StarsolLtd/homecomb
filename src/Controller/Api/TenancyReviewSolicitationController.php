<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\NotFoundException;
use App\Service\TenancyReviewSolicitationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class TenancyReviewSolicitationController extends AppController
{
    public function __construct(
        private TenancyReviewSolicitationService $tenancyReviewSolicitationService,
        protected SerializerInterface $serializer
    ) {
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
        try {
            $view = $this->tenancyReviewSolicitationService->getViewByCode($code);
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($view, Response::HTTP_OK);
    }
}
