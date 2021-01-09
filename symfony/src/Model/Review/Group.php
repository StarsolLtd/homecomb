<?php

namespace App\Model\Review;

class Group
{
    private string $title;
    private array $reviews;

    public function __construct(
        string $title,
        array $reviews
    ) {
        $this->title = $title;
        $this->reviews = $reviews;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return View[]
     */
    public function getReviews(): array
    {
        return $this->reviews;
    }
}
