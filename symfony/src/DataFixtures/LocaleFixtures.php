<?php

namespace App\DataFixtures;

use App\Entity\Locale;
use App\Entity\Postcode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LocaleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $localeNames = [
            'Birmingham',
            'Cambridge',
            'Clerkenwell',
            'Norwich',
        ];

        $locales = [];
        foreach ($localeNames as $localeName) {
            $locales[$localeName] = (new Locale())->setName($localeName);
        }

        $locales['Cambridge']
            ->addPostcode((new Postcode())->setPostcode('CB1'))
            ->addPostcode((new Postcode())->setPostcode('CB2'))
            ->addPostcode((new Postcode())->setPostcode('CB3'))
            ->addPostcode((new Postcode())->setPostcode('CB4'))
            ->addPostcode((new Postcode())->setPostcode('CB5'));

        /** @var Locale $locale */
        foreach ($locales as $locale) {
            $locale->setPublished(true);
            $manager->persist($locale);
        }

        $manager->flush();
    }
}
