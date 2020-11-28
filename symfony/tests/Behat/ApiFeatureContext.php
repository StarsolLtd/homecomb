<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Repository\PropertyRepository;
use App\Repository\ReviewRepository;
use Behat\Behat\Context\Context;
use Symfony\Component\Console\Output\BufferedOutput;
use Webmozart\Assert\Assert;

class ApiFeatureContext implements Context
{
    private BufferedOutput $output;
    private PropertyRepository $propertyRepository;
    private ReviewRepository $reviewRepository;

    public function __construct(
        PropertyRepository $propertyRepository,
        ReviewRepository $reviewRepository
    ) {
        $this->propertyRepository = $propertyRepository;
        $this->reviewRepository = $reviewRepository;
        $this->output = new BufferedOutput();
    }

    /**
     * @Then a Property should exist in the database with addressLine1 of :arg1 and postcode of :arg2
     */
    public function aPropertyShouldExistInTheDatabaseWithAddresslineOfAndPostcodeOf(string $arg1, string $arg2)
    {
        $property = $this->propertyRepository->findOneBy(
            [
                'addressLine1' => $arg1,
                'postcode' => $arg2,
            ]
        );
        Assert::notNull($property);
    }

    /**
     * @Then a Review should exist in the database with propertyId of :arg1 and title of :arg2
     */
    public function aReviewShouldExistInTheDatabaseWithPropertyIdOfAndTitleOf(string $arg1, string $arg2)
    {
        $property = $this->propertyRepository->find($arg1);

        $review = $this->reviewRepository->findOneBy(
            [
                'property' => $property,
                'title' => $arg2,
            ]
        );
        Assert::notNull($review);
    }
}
