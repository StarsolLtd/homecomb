<?php

namespace App\Repository\Survey;

use App\Entity\Survey\Answer;
use App\Entity\Survey\Question;
use App\Entity\Survey\Response;

interface AnswerRepositoryInterface
{
    public function findOneById(int $id): Answer;

    public function findLastPublished(): ?Answer;

    /**
     * @return Answer[]
     */
    public function findByQuestionAndResponse(Question $question, Response $response): array;
}
