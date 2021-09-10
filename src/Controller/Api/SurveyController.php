<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\NotFoundException;
use App\Factory\InteractionFactory;
use App\Model\Survey\SubmitAnswerInput;
use App\Service\SurveyService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SurveyController extends AppController
{
    public function __construct(
        private SurveyService $surveyService,
        protected SerializerInterface $serializer,
        protected InteractionFactory $interactionFactory,
    ) {
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

    /**
     * @Route (
     *     "/api/s/answer",
     *     name="api-survey-answer",
     *     methods={"POST"}
     * )
     */
    public function answer(Request $request): JsonResponse
    {
        try {
            /** @var SubmitAnswerInput $input */
            $input = $this->serializer->deserialize($request->getContent(), SubmitAnswerInput::class, 'json');
        } catch (Exception $e) {
            $this->addDeserializationFailedFlashMessage();

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        try {
            $output = $this->surveyService->answer(
                $input,
                $this->getUserInterface(),
                $this->getRequestDetails($request)
            );
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($output, Response::HTTP_CREATED);
    }
}
