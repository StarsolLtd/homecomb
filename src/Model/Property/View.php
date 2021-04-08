<?php

namespace App\Model\Property;

use App\Model\Review\View as ReviewView;

class View
{
    private string $slug;
    private ?string $addressLine1;
    private ?string $postcode;
    private array $reviews;

    public function __construct(
        string $slug,
        ?string $addressLine1,
        ?string $postcode,
        array $reviews
    ) {
        $this->slug = $slug;
        $this->addressLine1 = $addressLine1;
        $this->postcode = $postcode;
        $this->reviews = $reviews;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    /**
     * @return ReviewView[]
     */
    public function getReviews(): array
    {
        return $this->reviews;
    }
}
