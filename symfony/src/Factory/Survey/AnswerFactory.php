<?php

namespace App\Factory\Survey;

use App\Entity\Survey\Answer;
use App\Entity\Survey\Response;
use App\Model\Survey\SubmitAnswerInput;
use App\Repository\Survey\QuestionRepository;

class AnswerFactory
{
    private QuestionRepository $questionRepository;

    public function __construct(
        QuestionRepository $questionRepository
    ) {
        $this->questionRepository = $questionRepository;
    }

    public function createEntityFromSubmitInput(SubmitAnswerInput $input, Response $response): Answer
    {
        $question = $this->questionRepository->findOnePublishedById($input->getQuestionId());

        return (new Answer())
            ->setQuestion($question)
            ->setResponse($response)
            ->setContent($input->getContent())
        ;
    }
}
