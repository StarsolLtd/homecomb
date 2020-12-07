<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AppController extends AbstractController
{
    protected function getUserInterface(): ?UserInterface
    {
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            return $user;
        }

        return null;
    }
}
