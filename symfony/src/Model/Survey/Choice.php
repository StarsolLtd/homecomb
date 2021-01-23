<?php

namespace App\Model\Survey;

class Choice
{
    private int $id;
    private string $name;
    private ?string $help;
    private int $sortOrder;

    public function __construct(
        int $id,
        string $name,
        ?string $help,
        int $sortOrder
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->help = $help;
        $this->sortOrder = $sortOrder;
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
