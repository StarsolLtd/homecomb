<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TestFixtures extends Fixture
{
    public const TEST_USER_STANDARD_EMAIL = 'test.user.standard@starsol.co.uk';
    public const TEST_USER_AGENCY_ADMIN_EMAIL = 'test.agency.admin@starsol.co.uk';

    public const TEST_AGENCY_SLUG = 'testerton';
    public const TEST_BRANCH_SLUG = 'branchslug';
    public const TEST_PROPERTY_SLUG = 'propertyslug';

    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = (new User())
            ->setEmail(self::TEST_USER_STANDARD_EMAIL)
            ->setTitle('Mr')
            ->setFirstName('Terry')
            ->setLastName('Sterling')
            ->setIsVerified(true);

        $user1->setPassword($this->userPasswordEncoder->encodePassword($user1, 'Password1'));
        $manager->persist($user1);

        $user2 = (new User())
            ->setEmail(self::TEST_USER_AGENCY_ADMIN_EMAIL)
            ->setTitle('Ms')
            ->setFirstName('Fiona')
            ->setLastName('Dutton')
            ->setIsVerified(true);

        $user2->setPassword($this->userPasswordEncoder->encodePassword($user2, 'Password2'));
        $manager->persist($user2);

        $agency = (new Agency())
            ->setName('Testerton Lettings')
            ->setPublished(true)
            ->setSlug(self::TEST_AGENCY_SLUG);
        $manager->persist($agency);

        $branch = (new Branch())
            ->setAgency($agency)
            ->setName('Dereham')
            ->setPublished(true)
            ->setSlug(self::TEST_BRANCH_SLUG);
        $manager->persist($branch);

        $property = (new Property())
            ->setAddressLine1('Testerton Hall')
            ->setPostcode('NR21 7ES')
            ->setCountryCode('UK')
            ->setSlug(self::TEST_PROPERTY_SLUG);
        $manager->persist($property);

        $agency->addAdminUser($user2);

        $review = (new Review())
            ->setUser($user1)
            ->setProperty($property)
            ->setBranch($branch)
            ->setTitle('What a lovely cupboard under the stairs')
            ->setAuthor('Terrence S.')
            ->setContent(
                'Just wow, the cupboard under the stairs led to a basement where the was a dragon guarding '
                .'a UFO. Eventually I talked the dragon into letting me take the UFO outside. I did and I flew it '
                .'to Fakenham.'
            )
            ->setOverallStars(5)
            ->setAgencyStars(null)
            ->setLandlordStars(null)
            ->setPropertyStars(5)
            ->setPublished(true);
        $manager->persist($review);

        $manager->flush();
    }
}
