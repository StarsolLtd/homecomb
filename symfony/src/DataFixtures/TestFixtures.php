<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Locale;
use App\Entity\Property;
use App\Entity\Review;
use App\Entity\ReviewSolicitation;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TestFixtures extends AbstractDataFixtures
{
    public const TEST_USER_STANDARD_EMAIL = 'test.user.standard@starsol.co.uk';
    public const TEST_USER_AGENCY_1_ADMIN_EMAIL = 'test.agency.1.admin@starsol.co.uk';
    public const TEST_USER_AGENCY_2_ADMIN_EMAIL = 'test.agency.2.admin@starsol.co.uk';

    public const TEST_AGENCY_1_SLUG = 'testerton';
    public const TEST_AGENCY_2_SLUG = 'checkerfield';
    public const TEST_BRANCH_101_SLUG = 'branch101slug';
    public const TEST_BRANCH_102_SLUG = 'branch102slug';
    public const TEST_BRANCH_201_SLUG = 'branch201slug';
    public const TEST_LOCALE_SLUG = 'fakenham';
    public const TEST_PROPERTY_SLUG = 'propertyslug';
    public const TEST_REVIEW_SOLICITATION_CODE = '73d2d50d17e8c1bbb05b8fddb3918033f2daf589';

    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    protected function getEnvironments(): array
    {
        return ['test', 'e2e'];
    }

    protected function doLoad(ObjectManager $manager): void
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
            ->setEmail(self::TEST_USER_AGENCY_1_ADMIN_EMAIL)
            ->setTitle('Ms')
            ->setFirstName('Fiona')
            ->setLastName('Dutton')
            ->setIsVerified(true);

        $user2->setPassword($this->userPasswordEncoder->encodePassword($user2, 'Password2'));
        $manager->persist($user2);

        $user3 = (new User())
            ->setEmail(self::TEST_USER_AGENCY_2_ADMIN_EMAIL)
            ->setTitle('Ms')
            ->setFirstName('Ruth')
            ->setLastName('Pound')
            ->setIsVerified(true);

        $user3->setPassword($this->userPasswordEncoder->encodePassword($user3, 'Password3'));
        $manager->persist($user3);

        $branch101 = (new Branch())
            ->setName('Dereham')
            ->setPublished(true)
            ->setSlug(self::TEST_BRANCH_101_SLUG)
        ;
        $manager->persist($branch101);

        $branch102 = (new Branch())
            ->setName('Guist')
            ->setPublished(true)
            ->setSlug(self::TEST_BRANCH_102_SLUG)
        ;
        $manager->persist($branch102);

        $branch201 = (new Branch())
            ->setName('Reepham')
            ->setPublished(true)
            ->setSlug(self::TEST_BRANCH_201_SLUG)
        ;
        $manager->persist($branch201);

        $agency1 = (new Agency())
            ->setName('Testerton Lettings')
            ->setPublished(true)
            ->setSlug(self::TEST_AGENCY_1_SLUG)
            ->addBranch($branch101)
            ->addBranch($branch102)
            ->addAdminUser($user2)
        ;
        $manager->persist($agency1);

        $agency2 = (new Agency())
            ->setName('Checkerfield Homes')
            ->setPublished(true)
            ->setSlug(self::TEST_AGENCY_2_SLUG)
            ->addBranch($branch201)
            ->addAdminUser($user3)
        ;
        $manager->persist($agency2);

        $property = (new Property())
            ->setAddressLine1('Testerton Hall')
            ->setPostcode('NR21 7ES')
            ->setCountryCode('UK')
            ->setSlug(self::TEST_PROPERTY_SLUG);
        $manager->persist($property);

        $review = (new Review())
            ->setUser($user1)
            ->setProperty($property)
            ->setBranch($branch101)
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

        $locale = (new Locale())
            ->setName('Fakenham')
            ->setPublished(true)
            ->addReview($review)
        ;
        $manager->persist($locale);

        $rs = (new ReviewSolicitation())
            ->setBranch($branch101)
            ->setSenderUser($user2)
            ->setProperty($property)
            ->setRecipientFirstName('Anna')
            ->setRecipientLastName('Testinova')
            ->setRecipientEmail('anna.testinova@starsol.co.uk')
            ->setCode(self::TEST_REVIEW_SOLICITATION_CODE)
        ;

        $manager->persist($rs);

        $manager->flush();
    }
}
