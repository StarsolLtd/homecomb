<?php

namespace App\Model;

class SuggestPropertyInput
{
    public function __construct(
        private string $search,
    ) {
    }

    public function getSearch(): string
    {
        return $this->search;
    }
}
