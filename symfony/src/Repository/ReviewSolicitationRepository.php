<?php

namespace App\Repository;

use App\Entity\ReviewSolicitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
}
