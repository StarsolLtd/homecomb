<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\Review;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DemoFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $agency = (new Agency())
            ->setName('Cambridge Residential')
            ->setPostcode('CB2 8PE')
            ->setCountryCode('UK')
            ->setCreatedAt(new DateTime('2020-11-27 12:00:00'))
            ->setUpdatedAt(new DateTime('2020-11-27 12:00:00'));

        $manager->persist($agency);

        $branch = (new Branch())
            ->setAgency($agency)
            ->setName('Arbury')
            ->setCreatedAt(new DateTime('2020-11-27 12:00:00'))
            ->setUpdatedAt(new DateTime('2020-11-27 12:00:00'));

        $manager->persist($branch);

        $property = (new Property())
            ->setVendorPropertyId('ZmM5Yzc5MzMyODAyZTc4IDE3MDQ0OTcyIDMzZjhlNDFkNGU1MzY0Mw==')
            ->setAddressLine1('249 Victoria Road')
            ->setCity('Cambridge')
            ->setPostcode('CB4 3LF')
            ->setCountryCode('UK')
            ->setCreatedAt(new DateTime('2020-11-27 12:00:00'))
            ->setUpdatedAt(new DateTime('2020-11-27 12:00:00'));

        $manager->persist($property);

        $user = (new User())
            ->setEmail('jack@starsol.co.uk')
            ->setCreatedAt(new DateTime('2020-11-27 12:00:00'))
            ->setUpdatedAt(new DateTime('2020-11-27 12:00:00'));

        $manager->persist($user);

        $review = (new Review())
            ->setUser($user)
            ->setProperty($property)
            ->setBranch($branch)
            ->setTitle('Pleasant two year stay in great location')
            ->setAuthor('Jack Parnell')
            ->setContent(
                'I rented this home for two years. It is in a great location close to the city centre and the '
                .'agents were always pleasant when I dealt with them. The property was furnished, and while the '
                .'kitchen fittings were pretty good, the rest of the decor I think had not changed since the 1970s.'
            )
            ->setStars(4)
            ->setCreatedAt(new DateTime('2020-11-27 12:00:00'))
            ->setUpdatedAt(new DateTime('2020-11-27 12:00:00'));

        $manager->persist($review);

        $manager->flush();
    }
}
