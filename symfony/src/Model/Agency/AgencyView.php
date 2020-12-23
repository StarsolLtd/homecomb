<?php

namespace App\Model\Agency;

class AgencyView
{
    private string $slug;
    private string $name;

    public function __construct(
        string $slug,
        string $name
    ) {
        $this->slug = $slug;
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
