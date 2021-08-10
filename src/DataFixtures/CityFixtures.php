<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Util\CityHelper;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends AbstractDataFixtures
{
    private CityHelper $cityHelper;

    public const CAMBRIDGE_SLUG = '4ab26f4387989e70';
    public const KINGS_LYNN_SLUG = '8475b53127850aba';

    public function __construct(
        CityHelper $cityHelper
    ) {
        $this->cityHelper = $cityHelper;
    }

    protected function getEnvironments(): array
    {
        return ['dev', 'prod'];
    }

    protected function doLoad(ObjectManager $manager): void
    {
        $cities = [];

        $cities[] = (new City())
            ->setName('Cambridge')
            ->setCounty('Cambridgeshire')
            ->setSlug(self::CAMBRIDGE_SLUG)
            ->setCountryCode('UK')
        ;

        $cities[] = (new City())
            ->setName("King's Lynn")
            ->setCounty('Norfolk')
            ->setSlug(self::KINGS_LYNN_SLUG)
            ->setCountryCode('UK')
        ;

        foreach ($cities as $city) {
            $city->setPublished(true);
            $city->setSlug($this->cityHelper->generateSlug($city));
            $manager->persist($city);

            $this->addReference('city-'.$city->getSlug(), $city);
        }

        $manager->flush();
    }
}
