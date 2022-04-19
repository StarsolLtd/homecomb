<?php

namespace App\Model\TenancyReview;

class Group
{
    /**
     * @param View[] $tenancyReviews
     */
    public function __construct(
        private string $title,
        private array $tenancyReviews,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return View[]
     */
    public function getTenancyReviews(): array
    {
        return $this->tenancyReviews;
    }
}
