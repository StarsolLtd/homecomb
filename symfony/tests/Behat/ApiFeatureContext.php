<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Repository\PropertyRepository;
use Behat\Behat\Context\Context;
use Symfony\Component\Console\Output\BufferedOutput;
use Webmozart\Assert\Assert;

class ApiFeatureContext implements Context
{
    private BufferedOutput $output;
    private PropertyRepository $propertyRepository;

    public function __construct(
        PropertyRepository $propertyRepository
    ) {
        $this->propertyRepository = $propertyRepository;
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
}
