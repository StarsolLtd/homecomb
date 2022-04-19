<?php

namespace App\Factory;

use App\Entity\User;
use App\Model\User\RegisterInputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFactory
{
    public function __construct(
        private UserPasswordEncoderInterface $userPasswordEncoder,
    ) {
    }

    public function createEntityFromRegisterInput(RegisterInputInterface $input): User
    {
        $user = (new User())
            ->setEmail($input->getEmail())
            ->setFirstName($input->getFirstName())
            ->setLastName($input->getLastName())
        ;

        $user->setPassword(
            $this->userPasswordEncoder->encodePassword(
                $user,
                $input->getPlainPassword()
            )
        );

        return $user;
    }
}
