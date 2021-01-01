<?php

namespace App\Factory;

use App\Entity\User;
use App\Model\User\RegisterInput;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFactory
{
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function createEntityFromRegisterInput(RegisterInput $input): User
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
