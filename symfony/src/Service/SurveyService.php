<?php

namespace App\Service;

use App\Factory\Survey\AnswerFactory;
use App\Factory\Survey\ResponseFactory;
use App\Factory\Survey\SurveyFactory;
use App\Model\Interaction\RequestDetails;
use App\Model\Survey\CreateResponseInput;
use App\Model\Survey\CreateResponseOutput;
use App\Model\Survey\SubmitAnswerInput;
use App\Model\Survey\SubmitAnswerOutput;
use App\Model\Survey\View;
use App\Repository\Survey\QuestionRepository;
use App\Repository\Survey\ResponseRepository;
use App\Repository\Survey\SurveyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SurveyService
{
    private EntityManagerInterface $entityManager;
    private InteractionService $interactionService;
    private SessionService $sessionService;
    private UserService $userService;
    private AnswerFactory $answerFactory;
    private ResponseFactory $responseFactory;
    private SurveyFactory $surveyFactory;
    private QuestionRepository $questionRepository;
    private ResponseRepository $responseRepository;
    private SurveyRepository $surveyRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        InteractionService $interactionService,
        SessionService $sessionService,
        UserService $userService,
        AnswerFactory $answerFactory,
        ResponseFactory $responseFactory,
        SurveyFactory $surveyFactory,
        QuestionRepository $questionRepository,
        ResponseRepository $responseRepository,
        SurveyRepository $surveyRepository
    ) {
        $this->entityManager = $entityManager;
        $this->interactionService = $interactionService;
        $this->sessionService = $sessionService;
        $this->userService = $userService;
        $this->answerFactory = $answerFactory;
        $this->responseFactory = $responseFactory;
        $this->surveyFactory = $surveyFactory;
        $this->questionRepository = $questionRepository;
        $this->responseRepository = $responseRepository;
        $this->surveyRepository = $surveyRepository;
    }

    public function getViewBySlug(string $slug): View
    {
        $survey = $this->surveyRepository->findOnePublishedBySlug($slug);

        return $this->surveyFactory->createViewFromEntity($survey);
    }

    public function createResponse(CreateResponseInput $input, ?UserInterface $user): CreateResponseOutput
    {
        $user = $this->userService->getUserEntityOrNullFromUserInterface($user);

        $response = $this->responseFactory->createEntityFromCreateInput($input, $user);

        $this->entityManager->persist($response);
        $this->entityManager->flush();

        $this->sessionService->set('survey_'.$response->getSurvey()->getId().'_response_id', $response->getId());

        return new CreateResponseOutput(true);
    }

    public function answer(
        SubmitAnswerInput $input,
        ?RequestDetails $requestDetails = null
    ): SubmitAnswerOutput {
        $question = $this->questionRepository->findOnePublishedById($input->getQuestionId());
        $surveyId = $question->getSurvey()->getId();
        $responseId = $this->sessionService->get('survey_'.$surveyId.'_response_id');

        $response = $this->responseRepository->findOneById($responseId);

        $answer = $this->answerFactory->createEntityFromSubmitInput($input, $response);

        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        // TODO log interaction

        return new SubmitAnswerOutput(true);
    }
}
