<?php

namespace App\Factory\Survey;

use App\Entity\Survey\Answer;
use App\Entity\Survey\Response;
use App\Model\Survey\SubmitAnswerInput;
use App\Repository\Survey\ChoiceRepository;
use App\Repository\Survey\QuestionRepository;

class AnswerFactory
{
    private ChoiceRepository $choiceRepository;
    private QuestionRepository $questionRepository;

    public function __construct(
        ChoiceRepository $choiceRepository,
        QuestionRepository $questionRepository
    ) {
        $this->choiceRepository = $choiceRepository;
        $this->questionRepository = $questionRepository;
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
