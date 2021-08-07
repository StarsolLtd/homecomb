<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Locale\Locale;
use App\Repository\LocaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Repository\LocaleRepository
 */
class LocaleRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private LocaleRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(Locale::class);
    }

    /**
     * @covers \App\Repository\LocaleRepository::findOneByUnique
     */
    public function testFindOnePublishedBySlug()
    {
        $locale = $this->repository->findOnePublishedBySlug('fakenham');

        $this->assertNotNull($locale);
        $this->assertEquals('Fakenham', $locale->getName());
        $this->assertTrue($locale->isPublished());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->entityManager, $this->repository);
    }
}
