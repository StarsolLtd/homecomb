<?php

namespace App\Repository\Survey;

use App\Entity\Survey\Choice;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function sprintf;

/**
 * @method Choice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Choice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Choice[]    findAll()
 * @method Choice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Choice::class);
    }

    public function findOnePublishedById(int $id): Choice
    {
        $choice = $this->findOneBy(
            [
                'id' => $id,
                'published' => true,
            ]
        );

        if (null === $choice) {
            throw new NotFoundException(sprintf('No published choice with ID %d could be found.', $id));
        }

        return $choice;
    }
}
