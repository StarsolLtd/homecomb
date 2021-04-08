<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Vote\CommentVote;
use App\Entity\Vote\ReviewVote;
use App\Entity\Vote\Vote;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function sprintf;

/**
 * @method Vote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vote[]    findAll()
 * @method Vote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    public function findOneById(int $id): Vote
    {
        $vote = $this->find($id);

        if (null === $vote) {
            throw new NotFoundException(sprintf('Vote %d could be found.', $id));
        }

        return $vote;
    }

    public function findOneReviewVoteByUserAndEntity(User $user, int $entityId): ?ReviewVote
    {
        $qb = $this->createQueryBuilder('v');
        $qb->where($qb->expr()->isInstanceOf('v', ReviewVote::class))
            ->andWhere('v.entityId = :entityId')
            ->andWhere('v.user = :user')
            ->setMaxResults(1)
            ->setParameter('entityId', $entityId)
            ->setParameter('user', $user)
        ;

        $query = $qb->getQuery();

        $result = $query->getResult();

        return $result[0] ?? null;
    }

    public function findOneCommentVoteByUserAndEntity(User $user, int $entityId): ?CommentVote
    {
        $qb = $this->createQueryBuilder('v');
        $qb->where($qb->expr()->isInstanceOf('v', CommentVote::class))
            ->andWhere('v.entityId = :entityId')
            ->andWhere('v.user = :user')
            ->setMaxResults(1)
            ->setParameter('entityId', $entityId)
            ->setParameter('user', $user)
        ;

        $query = $qb->getQuery();

        $result = $query->getResult();

        return $result[0] ?? null;
    }
}
