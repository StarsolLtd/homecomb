<?php

namespace App\Repository\Survey;

use App\Entity\Survey\Survey;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function sprintf;

/**
 * @method Survey|null find($id, $lockMode = null, $lockVersion = null)
 * @method Survey|null findOneBy(array $criteria, array $orderBy = null)
 * @method Survey[]    findAll()
 * @method Survey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SurveyRepository extends ServiceEntityRepository implements SurveyRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Survey::class);
    }

    public function findOnePublishedBySlug(string $slug): Survey
    {
        $survey = $this->findOneBy(
            [
                'slug' => $slug,
                'published' => true,
            ]
        );

        if (null === $survey) {
            throw new NotFoundException(sprintf('No published survey with slug %s could be found.', $slug));
        }

        return $survey;
    }
}
