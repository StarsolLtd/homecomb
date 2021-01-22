<?php

namespace App\Model\Survey;

class SubmitAnswerInput
{
    private int $questionId;
    private ?string $content;
    private ?int $rating;

    public function __construct(
        int $questionId,
        ?string $content = null,
        ?int $rating = null
    ) {
        $this->questionId = $questionId;
        $this->content = $content;
        $this->rating = $rating;
    }

    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }
}
