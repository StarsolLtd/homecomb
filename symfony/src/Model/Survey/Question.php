<?php

namespace App\Model\Survey;

class Question
{
    private int $id;
    private string $type;
    private string $content;
    private ?string $help;
    private ?string $highMeaning;
    private ?string $lowMeaning;
    private int $sortOrder;

    public function __construct(
        int $id,
        string $type,
        string $content,
        ?string $help,
        ?string $highMeaning,
        ?string $lowMeaning,
        int $sortOrder
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->content = $content;
        $this->help = $help;
        $this->highMeaning = $highMeaning;
        $this->lowMeaning = $lowMeaning;
        $this->sortOrder = $sortOrder;
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
}
