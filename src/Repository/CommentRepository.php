<?php

namespace App\Repository;

use App\Entity\Comment\Comment;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function sprintf;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CommentRepository extends ServiceEntityRepository implements CommentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findOnePublishedById(int $id): Comment
    {
        $comment = $this->findOneBy(
            [
                'id' => $id,
                'published' => true,
            ]
        );

        if (null === $comment) {
            throw new NotFoundException(sprintf('Comment %d could be found.', $id));
        }

        return $comment;
    }

    public function findLastPublished(): ?Comment
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
