<?php

namespace App\Model\Survey;

class View
{
    public function __construct(
        private string $slug,
        private string $title,
        private ?string $description,
        private array $questions,
    ) {
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return Question[]
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }
}
