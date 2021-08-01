<?php

namespace App\Model\TenancyReview;

class Group
{
    private string $title;
    private array $tenancyReviews;

    public function __construct(
        string $title,
        array $tenancyReviews
    ) {
        $this->title = $title;
        $this->tenancyReviews = $tenancyReviews;
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
