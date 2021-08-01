<?php

namespace App\Repository;

use App\Entity\TenancyReviewSolicitation;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TenancyReviewSolicitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TenancyReviewSolicitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TenancyReviewSolicitation[]    findAll()
 * @method TenancyReviewSolicitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TenancyReviewSolicitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TenancyReviewSolicitation::class);
    }

    public function findOneUnfinishedByCode(string $code): TenancyReviewSolicitation
    {
        $rs = $this->findOneBy(
            [
                'code' => $code,
                'tenancyReview' => null,
            ]
        );

        if (null === $rs) {
            throw new NotFoundException(sprintf('No unfinished review solicitation with code %s could be found.', $code));
        }

        return $rs;
    }

    public function findOneByCodeOrNull(string $code): ?TenancyReviewSolicitation
    {
        return $this->findOneBy(['code' => $code]);
    }
}
