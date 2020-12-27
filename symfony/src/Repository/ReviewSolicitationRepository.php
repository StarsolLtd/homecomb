<?php

namespace App\Repository;

use App\Entity\ReviewSolicitation;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function sprintf;

/**
 * @method ReviewSolicitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReviewSolicitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReviewSolicitation[]    findAll()
 * @method ReviewSolicitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewSolicitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReviewSolicitation::class);
    }

    public function findOneUnfinishedByCode(string $code): ReviewSolicitation
    {
        $rs = $this->findOneBy(
            [
                'code' => $code,
                'review' => null,
            ]
        );

        if (null === $rs) {
            throw new NotFoundException(sprintf('No unfinished review solicitation with code %s could be found.', $code));
        }

        return $rs;
    }
}
