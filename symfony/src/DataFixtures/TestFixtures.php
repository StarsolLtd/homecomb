<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TestFixtures extends Fixture
{
    public const TEST_USER_STANDARD_EMAIL = 'test.user.standard@starsol.co.uk';

    public const TEST_AGENCY_SLUG = 'testerton';

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

        $agency = (new Agency())
            ->setName('Testerton Lettings')
            ->setPublished(true)
            ->setSlug(self::TEST_AGENCY_SLUG);
        $manager->persist($agency);

        $manager->flush();
    }
}
