<?php

namespace App\Factory\Survey;

use App\Entity\Survey\Answer;
use App\Entity\Survey\Response;
use App\Model\Survey\SubmitAnswerInputInterface;
use App\Repository\Survey\ChoiceRepositoryInterface;
use App\Repository\Survey\QuestionRepositoryInterface;

class AnswerFactory
{
    public function __construct(
        private ChoiceRepositoryInterface $choiceRepository,
        private QuestionRepositoryInterface $questionRepository,
    ) {
    }

    public function createEntityFromSubmitInput(SubmitAnswerInputInterface $input, Response $response): Answer
    {
        $question = $this->questionRepository->findOnePublishedById($input->getQuestionId());

        $answer = (new Answer())
            ->setQuestion($question)
            ->setResponse($response)
            ->setContent($input->getContent())
            ->setRating($input->getRating())
        ;

        $choiceId = $input->getChoiceId();

        $choice = null !== $choiceId
            ? $this->choiceRepository->findOnePublishedById($choiceId)
            : null;

        if ($choice) {
            $answer->addChoice($choice);
        }

        return $answer;
    }
}
