<?php

namespace App\Service;

use App\Entity\Survey\Question;
use App\Entity\Survey\Response;
use App\Factory\Survey\AnswerFactory;
use App\Factory\Survey\SurveyFactory;
use App\Model\Interaction\RequestDetails;
use App\Model\Survey\SubmitAnswerInput;
use App\Model\Survey\SubmitAnswerOutput;
use App\Model\Survey\View;
use App\Repository\Survey\AnswerRepository;
use App\Repository\Survey\QuestionRepository;
use App\Repository\Survey\ResponseRepository;
use App\Repository\Survey\SurveyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SurveyService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private InteractionService $interactionService,
        private ResponseService $responseService,
        private SessionService $sessionService,
        private AnswerFactory $answerFactory,
        private SurveyFactory $surveyFactory,
        private AnswerRepository $answerRepository,
        private QuestionRepository $questionRepository,
        private ResponseRepository $responseRepository,
        private SurveyRepository $surveyRepository
    ) {
    }

    public function getViewBySlug(string $slug): View
    {
        $survey = $this->surveyRepository->findOnePublishedBySlug($slug);

        return $this->surveyFactory->createViewFromEntity($survey);
    }

    public function answer(
        SubmitAnswerInput $input,
        ?UserInterface $user,
        ?RequestDetails $requestDetails = null
    ): SubmitAnswerOutput {
        $question = $this->questionRepository->findOnePublishedById($input->getQuestionId());
        $survey = $question->getSurvey();

        $key = 'survey_'.$survey->getId().'_response_id';

        $responseId = $this->sessionService->get($key);

        if (null !== $responseId) {
            $response = $this->responseRepository->findOneById($responseId);
        } else {
            $response = $this->responseService->create($survey, $user);
            $this->sessionService->set($key, $response->getId());
        }

        $this->removeExistingAnswers($question, $response);

        $answer = $this->answerFactory->createEntityFromSubmitInput($input, $response);

        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        $this->interactionService->record('Answer', $answer->getId(), $requestDetails, $user);

        return new SubmitAnswerOutput(true);
    }

    private function removeExistingAnswers(Question $question, Response $response): void
    {
        $existingAnswers = $this->answerRepository->findByQuestionAndResponse($question, $response);
        foreach ($existingAnswers as $answer) {
            $this->entityManager->remove($answer);
        }
    }
}
