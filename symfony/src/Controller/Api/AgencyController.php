<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Service\AgencyService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AgencyController extends AppController
{
    private AgencyService $agencyService;

    public function __construct(
        AgencyService $agencyService,
        SerializerInterface $serializer
    ) {
        $this->agencyService = $agencyService;
        $this->serializer = $serializer;
    }

    /**
     * @Route (
     *     "/api/agency/{slug}",
     *     name="api-agency-view",
     *     methods={"GET"}
     * )
     */
    public function view(string $slug, Request $request): JsonResponse
    {
        $view = $this->agencyService->getViewBySlug($slug);

        return $this->jsonResponse($view, Response::HTTP_OK);
    }
}
