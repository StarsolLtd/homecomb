<?php

namespace App\Model\Survey;

class Question
{
    /**
     * @param Choice[] $choices
     */
    public function __construct(
        private int $id,
        private string $type,
        private string $content,
        private ?string $help,
        private ?string $highMeaning,
        private ?string $lowMeaning,
        private int $sortOrder,
        private array $choices = [],
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function getHighMeaning(): ?string
    {
        return $this->highMeaning;
    }

    public function getLowMeaning(): ?string
    {
        return $this->lowMeaning;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @return Choice[]
     */
    public function getChoices(): array
    {
        return $this->choices;
    }
}
