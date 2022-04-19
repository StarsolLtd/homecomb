<?php

namespace App\Model\Survey;

interface SubmitAnswerInputInterface
{
    public function getQuestionId(): int;

    public function getContent(): ?string;

    public function getChoiceId(): ?int;

    public function getRating(): ?int;
}
