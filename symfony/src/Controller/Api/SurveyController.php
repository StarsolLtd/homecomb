<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\NotFoundException;
use App\Service\SurveyService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SurveyController extends AppController
{
    private SurveyService $surveyService;

    public function __construct(
        SurveyService $surveyService,
        SerializerInterface $serializer
    ) {
        $this->surveyService = $surveyService;
        $this->serializer = $serializer;
    }

    /**
     * @Route (
     *     "/api/s/{slug}",
     *     name="api-survey-view",
     *     methods={"GET"}
     * )
     */
    public function view(string $slug, Request $request): JsonResponse
    {
        try {
            $view = $this->surveyService->getViewBySlug($slug);
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($view, Response::HTTP_OK);
    }
}
