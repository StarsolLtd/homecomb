<?php

namespace App\Tests\Functional\Repository\Locale;

use App\Entity\Locale\Locale;
use App\Repository\Locale\LocaleRepository;
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

    /**
     * @covers \App\Repository\LocaleRepository::findBySearchQuery
     * Test a lower case search returns a result where the first letter is capitalised.
     */
    public function testFindBySearchQuery1()
    {
        $results = $this->repository->findBySearchQuery('faken');

        $this->assertCount(1, $results);
        $this->assertEquals('Fakenham', $results->first()->getName());
    }

    /**
     * @covers \App\Repository\LocaleRepository::findBySearchQuery
     * Test multiple results are returned when there are multiple results.
     */
    public function testFindBySearchQuery2()
    {
        $results = $this->repository->findBySearchQuery('King');

        $this->assertCount(2, $results);
        $this->assertEquals("King's Lynn", $results[0]->getName());
        $this->assertEquals('Kingston upon Thames', $results[1]->getName());
    }

    /**
     * @covers \App\Repository\LocaleRepository::findBySearchQuery
     * Test a search query is trimmed.
     */
    public function testFindBySearchQuery3()
    {
        $results = $this->repository->findBySearchQuery("\tIslington ");

        $this->assertCount(1, $results);
        $this->assertEquals('Islington', $results->first()->getName());
    }

    /**
     * @covers \App\Repository\LocaleRepository::findBySearchQuery
     * Test no results are returned when there are no matches.
     */
    public function testFindBySearchQuery4()
    {
        $results = $this->repository->findBySearchQuery('Cluj-Napoca');

        $this->assertCount(0, $results);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->entityManager, $this->repository);
    }
}
