<?php

namespace App\Repository;

use App\Entity\Flag\Flag;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Flag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Flag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Flag[]    findAll()
 * @method Flag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class FlagRepository extends ServiceEntityRepository implements FlagRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flag::class);
    }

    public function findOneById(int $id): Flag
    {
        $flag = $this->find($id);

        if (null === $flag) {
            throw new NotFoundException(sprintf('Flag %d could be found.', $id));
        }

        return $flag;
    }
}
