<?php

namespace App\Model\Review;

use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;
use App\Model\Comment\Flat as FlatComment;
use App\Model\Property\Flat as FlatProperty;
use App\Model\Review\View;
use DateTime;

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
