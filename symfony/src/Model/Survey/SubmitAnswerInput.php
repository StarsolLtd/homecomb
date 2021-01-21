<?php

namespace App\Model\Survey;

class SubmitAnswerInput
{
    private int $questionId;
    private ?string $content;

    public function __construct(
        int $questionId,
        ?string $content = null
    ) {
        $this->questionId = $questionId;
        $this->content = $content;
    }

    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }
}
