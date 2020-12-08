<?php

namespace App\EventSubscriber;

use App\Entity\Flag;
use App\Service\UserService;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use function method_exists;
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
            BeforeEntityPersistedEvent::class => [['setCreatedAt', 1], ['setUser', 2]],
            BeforeEntityUpdatedEvent::class => ['setUpdatedAt'],
        ];
    }

    public function setCreatedAt(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (method_exists($entity, 'setCreatedAt')) {
            $entity->setCreatedAt(new DateTime());
        }
        if (method_exists($entity, 'setUpdatedAt')) {
            $entity->setUpdatedAt(new DateTime());
        }
    }

    public function setUpdatedAt(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (method_exists($entity, 'setUpdatedAt')) {
            $entity->setUpdatedAt(new DateTime());
        }
    }

    public function setUser(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Flag) {
            if (!$entity->getUser()) {
                $userEntity = $this->userService->getUserEntityFromUserInterface($this->security->getUser());
                $entity->setUser($userEntity);
            }
        }
    }
}
