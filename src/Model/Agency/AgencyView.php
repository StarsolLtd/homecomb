<?php

namespace App\Model\Agency;

use App\Model\Branch\Flat as FlatBranch;

class AgencyView
{
    public function __construct(
        private string $slug,
        private string $name,
        private array $branches,
    ) {
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return FlatBranch[]
     */
    public function getBranches(): array
    {
        return $this->branches;
    }
}
