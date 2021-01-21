<?php

namespace App\Service;

use App\Factory\Survey\AnswerFactory;
use App\Factory\Survey\SurveyFactory;
use App\Model\Interaction\RequestDetails;
use App\Model\Survey\SubmitAnswerInput;
use App\Model\Survey\SubmitAnswerOutput;
use App\Model\Survey\View;
use App\Repository\Survey\ResponseRepository;
use App\Repository\Survey\SurveyRepository;
use Doctrine\ORM\EntityManagerInterface;

class SurveyService
{
    private EntityManagerInterface $entityManager;
    private InteractionService $interactionService;
    private AnswerFactory $answerFactory;
    private SurveyFactory $surveyFactory;
    private ResponseRepository $responseRepository;
    private SurveyRepository $surveyRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        InteractionService $interactionService,
        AnswerFactory $answerFactory,
        SurveyFactory $surveyFactory,
        ResponseRepository $responseRepository,
        SurveyRepository $surveyRepository
    ) {
        $this->entityManager = $entityManager;
        $this->interactionService = $interactionService;
        $this->answerFactory = $answerFactory;
        $this->surveyFactory = $surveyFactory;
        $this->responseRepository = $responseRepository;
        $this->surveyRepository = $surveyRepository;
    }

    public function getViewBySlug(string $slug): View
    {
        $survey = $this->surveyRepository->findOnePublishedBySlug($slug);

        return $this->surveyFactory->createViewFromEntity($survey);
    }

    public function answer(
        SubmitAnswerInput $input,
        int $responseId,
        ?RequestDetails $requestDetails = null
    ): SubmitAnswerOutput {
        $response = $this->responseRepository->findOneById($responseId);

        $answer = $this->answerFactory->createEntityFromSubmitInput($input, $response);

        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        // TODO log interaction

        return new SubmitAnswerOutput(true);
    }
}
