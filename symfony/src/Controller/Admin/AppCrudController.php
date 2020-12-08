<?php

namespace App\Controller\Admin;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use function method_exists;

abstract class AppCrudController extends AbstractCrudController
{
    public function createEntity(string $entityFqcn): void
    {
        $entityInstance = new $entityFqcn();
        if (method_exists($entityInstance, 'setCreatedAt')) {
            $entityInstance->setCreatedAt(new DateTime());
        }
    }

    /** @phpstan-ignore-next-line */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (method_exists($entityInstance, 'setUpdatedAt')) {
            $entityInstance->setUpdatedAt(new DateTime());
        }
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }
}
