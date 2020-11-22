<?php

namespace App\Model;

class SuggestPropertyInput
{
    private string $search;

    public function __construct(
        string $search
    ) {
        $this->search = $search;
    }

    public function getSearch(): string
    {
        return $this->search;
    }
}
