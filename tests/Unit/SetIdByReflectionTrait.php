<?php

namespace App\Tests\Unit;

use ReflectionClass;

trait SetIdByReflectionTrait
{
    private function setIdByReflection(object $entity, int $id): void
    {
        $class = new ReflectionClass($entity);
        $property = $class->getProperty('id');
        $property->setAccessible(true);

        $property->setValue($entity, $id);
    }
}
