<?php

namespace App\Model\Agency;

use App\Model\Branch\Flat as FlatBranch;

class AgencyView
{
    private string $slug;
    private string $name;
    private array $branches;

    public function __construct(
        string $slug,
        string $name,
        array $branches
    ) {
        $this->slug = $slug;
        $this->name = $name;
        $this->branches = $branches;
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
