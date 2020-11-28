<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\Review;
use App\Entity\User;
use App\Util\PropertyHelper;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DemoFixtures extends Fixture
{
    private PropertyHelper $propertyHelper;

    public function __construct(
        PropertyHelper $propertyHelper
    ) {
        $this->propertyHelper = $propertyHelper;
    }

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

        $this->propertyHelper->generateSlug($property);

        $manager->persist($property);

        $user1 = (new User())
            ->setEmail('jack@starsol.co.uk')
            ->setCreatedAt(new DateTime('2020-11-27 12:00:00'))
            ->setUpdatedAt(new DateTime('2020-11-27 12:00:00'));

        $manager->persist($user1);

        $review1 = (new Review())
            ->setUser($user1)
            ->setProperty($property)
            ->setBranch($branch)
            ->setTitle('Pleasant two year stay in great location')
            ->setAuthor('Jack Parnell')
            ->setContent(
                'I rented this home for two years. It is in a great location close to the city centre and the '
                .'agents were always pleasant when I dealt with them. The property was furnished, and while the '
                .'kitchen fittings were pretty good, the rest of the decor I think had not changed since the 1970s.'
            )
            ->setOverallStars(4)
            ->setAgencyStars(5)
            ->setLandlordStars(null)
            ->setPropertyStars(3)
            ->setCreatedAt(new DateTime('2020-11-27 12:00:00'))
            ->setUpdatedAt(new DateTime('2020-11-27 12:00:00'));

        $manager->persist($review1);

        $user2 = (new User())
            ->setEmail('andrea@starsol.co.uk')
            ->setCreatedAt(new DateTime('2020-11-28 12:00:00'))
            ->setUpdatedAt(new DateTime('2020-11-28 12:00:00'));

        $manager->persist($user2);

        $review2 = (new Review())
            ->setUser($user2)
            ->setProperty($property)
            ->setBranch($branch)
            ->setTitle('I loved this place!')
            ->setAuthor('Andrea Németh')
            ->setContent(
                'This was the first home I ever rented and had a great couple of years here. It is right in the '
                .'middle of Cambridge, plenty of amenties within walking distance. It has a nice garden at the back '
                .'and the landlord kindly allowed us to keep our pet dog here. It is a great home!'
            )
            ->setOverallStars(5)
            ->setAgencyStars(null)
            ->setLandlordStars(5)
            ->setPropertyStars(5)
            ->setCreatedAt(new DateTime('2020-11-28 12:00:00'))
            ->setUpdatedAt(new DateTime('2020-11-28 12:00:00'));

        $manager->persist($review2);

        $manager->flush();
    }
}
