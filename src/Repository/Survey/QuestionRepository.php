<?php

namespace App\Repository\Survey;

use App\Entity\Survey\Question;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class QuestionRepository extends ServiceEntityRepository implements QuestionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findOnePublishedById(int $id): Question
    {
        $question = $this->findOneBy(
            [
                'id' => $id,
                'published' => true,
            ]
        );

        if (null === $question) {
            throw new NotFoundException(sprintf('No published question with ID %d could be found.', $id));
        }

        return $question;
    }

    public function findLastPublished(): ?Question
    {
        return $this->findOneBy(
            [
                'published' => true,
            ],
            [
                'id' => 'DESC',
            ]
        );
    }
}
