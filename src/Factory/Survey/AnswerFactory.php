<?php

namespace App\Factory\Survey;

use App\Entity\Survey\Answer;
use App\Entity\Survey\Response;
use App\Model\Survey\SubmitAnswerInput;
use App\Repository\Survey\ChoiceRepositoryInterface;
use App\Repository\Survey\QuestionRepositoryInterface;

class AnswerFactory
{
    public function __construct(
        private ChoiceRepositoryInterface $choiceRepository,
        private QuestionRepositoryInterface $questionRepository
    ) {
    }

    public function createEntityFromSubmitInput(SubmitAnswerInput $input, Response $response): Answer
    {
        $question = $this->questionRepository->findOnePublishedById($input->getQuestionId());

        $answer = (new Answer())
            ->setQuestion($question)
            ->setResponse($response)
            ->setContent($input->getContent())
            ->setRating($input->getRating())
        ;

        $choice = null !== $input->getChoiceId()
            ? $this->choiceRepository->findOnePublishedById($input->getChoiceId())
            : null;

        if ($choice) {
            $answer->addChoice($choice);
        }

        return $answer;
    }
}
