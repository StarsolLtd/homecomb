<?php

namespace App\Tests\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait LoginUserTrait
{
    protected static $container;

    private function loginUser(KernelBrowser $client, string $username): User
    {
        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail($username);
        $client->loginUser($user);

        return $user;
    }
}
