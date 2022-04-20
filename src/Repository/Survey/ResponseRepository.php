<?php

namespace App\Repository\Survey;

use App\Entity\Survey\Response;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Response|null find($id, $lockMode = null, $lockVersion = null)
 * @method Response|null findOneBy(array $criteria, array $orderBy = null)
 * @method Response[]    findAll()
 * @method Response[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ResponseRepository extends ServiceEntityRepository implements ResponseRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Response::class);
    }

    public function findOneById(int $id): Response
    {
        $response = $this->find($id);

        if (null === $response) {
            throw new NotFoundException(sprintf('Response %d could be found.', $id));
        }

        return $response;
    }
}
