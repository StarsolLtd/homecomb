<?php

namespace App\EventSubscriber;

use App\Entity\Flag\Flag;
use App\Service\UserService;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private UserService $userService;

    public function __construct(
        Security $security,
        UserService $userService
    ) {
        $this->security = $security;
        $this->userService = $userService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['setUser'],
        ];
    }

    public function setUser(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Flag) {
            if (!$entity->getUser()) {
                $userEntity = $this->userService->getUserEntityOrNullFromUserInterface($this->security->getUser());
                $entity->setUser($userEntity);
            }
        }
    }
}
