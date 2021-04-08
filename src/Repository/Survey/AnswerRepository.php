<?php

namespace App\Repository\Survey;

use App\Entity\Survey\Answer;
use App\Entity\Survey\Question;
use App\Entity\Survey\Response;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function sprintf;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    public function findOneById(int $id): Answer
    {
        $answer = $this->find($id);

        if (null === $answer) {
            throw new NotFoundException(sprintf('Answer %d could not be found.', $id));
        }

        return $answer;
    }

    public function findLastPublished(): ?Answer
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

    /**
     * @return Answer[]
     */
    public function findByQuestionAndResponse(Question $question, Response $response): array
    {
        return $this->findBy(
            [
                'question' => $question,
                'response' => $response,
            ],
            [
                'id' => 'DESC',
            ]
        );
    }
}
