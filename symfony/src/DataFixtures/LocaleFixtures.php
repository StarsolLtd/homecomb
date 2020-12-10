<?php

namespace App\DataFixtures;

use App\Entity\Locale;
use App\Entity\Postcode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LocaleFixtures extends Fixture
{
    private const CAMBRIDGE_POSTCODES = [
        'CB1', 'CB2', 'CB3', 'CB4', 'CB5',
    ];

    private const GIRTON_POSTCODES = [
        'CB3 0FH', 'CB3 0FW', 'CB3 0GJ', 'CB3 0GL', 'CB3 0GP', 'CB3 0JH', 'CB3 0JP', 'CB3 0JR', 'CB3 0JS', 'CB3 0JT',
        'CB3 0JW', 'CB3 0JY', 'CB3 0JZ', 'CB3 0LG', 'CB3 0LH', 'CB3 0LJ', 'CB3 0LL', 'CB3 0LN', 'CB3 0LQ', 'CB3 0LS',
        'CB3 0LT', 'CB3 0LU', 'CB3 0LW', 'CB3 0LX', 'CB3 0LY', 'CB3 0LZ', 'CB3 0NA', 'CB3 0ND', 'CB3 0NE', 'CB3 0NF',
        'CB3 0NG', 'CB3 0NH', 'CB3 0NJ', 'CB3 0NL', 'CB3 0NN', 'CB3 0NP', 'CB3 0NQ', 'CB3 0NR', 'CB3 0NS', 'CB3 0NW',
        'CB3 0NY', 'CB3 0PA', 'CB3 0PB', 'CB3 0PD', 'CB3 0PE', 'CB3 0PF', 'CB3 0PG', 'CB3 0PH', 'CB3 0PJ', 'CB3 0PL',
        'CB3 0PN', 'CB3 0PP', 'CB3 0PQ', 'CB3 0PR', 'CB3 0PS', 'CB3 0PU', 'CB3 0PW', 'CB3 0PX', 'CB3 0PY', 'CB3 0PZ',
        'CB3 0QA', 'CB3 0QB', 'CB3 0QD', 'CB3 0QE', 'CB3 0QF', 'CB3 0QG', 'CB3 0QH', 'CB3 0QL', 'CB3 0QN', 'CB3 0QQ',
        'CB3 0QR', 'CB3 0QW', 'CB3 0RX', 'CB3 0RY', 'CB3 0XA', 'CB3 0XB', 'CB3 0JG', 'CB3 0JX',
    ];

    public function load(ObjectManager $manager): void
    {
        $localeNames = [
            'Birmingham',
            'Cambridge',
            'Clerkenwell',
            'Girton',
            'Norwich',
        ];

        $locales = [];
        foreach ($localeNames as $localeName) {
            $locales[$localeName] = (new Locale())->setName($localeName);
        }

        foreach (self::CAMBRIDGE_POSTCODES as $postcode) {
            $locales['Cambridge']->addPostcode((new Postcode())->setPostcode($postcode));
        }

        foreach (self::GIRTON_POSTCODES as $postcode) {
            $locales['Girton']->addPostcode((new Postcode())->setPostcode($postcode));
        }

        /** @var Locale $locale */
        foreach ($locales as $locale) {
            $locale->setPublished(true);
            $manager->persist($locale);
        }

        $manager->flush();
    }
}
