<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Branch;

/**
 * @covers \App\Entity\Branch
 */
final class BranchTest extends AbstractEntityTestCase
{
    protected array $values = [
        'name' => 'Branch Name',
        'telephone' => '07123456789',
        'email' => 'branch@starsol.co.uk',
        'slug' => 'test-branch-slug',
        'published' => true,
    ];

    protected function getEntity(): Branch
    {
        $entity = new Branch();
        $entity = $this->setPropertiesFromValuesArray($entity);
        assert($entity instanceof Branch);

        return $entity;
    }
}
