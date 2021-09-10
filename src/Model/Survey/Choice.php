<?php

namespace App\Model\Survey;

class Choice
{
    public function __construct(
        private int $id,
        private string $name,
        private ?string $help,
        private int $sortOrder,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }
}
