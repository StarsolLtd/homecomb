<?php

namespace App\Model\Survey;

class SubmitAnswerInput implements SubmitAnswerInputInterface
{
    public function __construct(
        private int $questionId,
        private ?string $content = null,
        private ?int $choiceId = null,
        private ?int $rating = null,
    ) {
    }

    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getChoiceId(): ?int
    {
        return $this->choiceId;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }
}
