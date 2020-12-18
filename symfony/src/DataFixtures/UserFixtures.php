<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = (new User())
            ->setEmail('jack@starsol.co.uk')
            ->setTitle('Mr')
            ->setFirstName('Jack')
            ->setLastName('Parnell')
            ->setRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);

        $user1->setPassword($this->userPasswordEncoder->encodePassword($user1, 'Long_Foggy_Drive_2020'));

        $manager->persist($user1);

        $user2 = (new User())
            ->setEmail('gina@starsol.co.uk')
            ->setTitle('Ms')
            ->setFirstName('Gina')
            ->setLastName('Pawel')
            ->setRoles(['ROLE_ADMIN', 'ROLE_MODERATOR']);

        $user2->setPassword($this->userPasswordEncoder->encodePassword($user2, 'Juggling_2020'));

        $manager->persist($user2);

        $manager->flush();
    }
}
