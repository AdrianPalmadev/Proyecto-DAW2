<?php

namespace App\Repository;

use App\Entity\Nurse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Nurse>
 */
class NurseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Nurse::class);
    }

    public function findByUser(string $usuario): ?Nurse
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.user = :u')
            ->setParameter('u', $usuario)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAll(): array
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function login($usuario, $password): ?Nurse
    {
        $nurse = $this->createQueryBuilder('n')
            ->andWhere('n.user = :u')
            ->setParameter('u', $usuario)
            ->getQuery()
            ->getOneOrNullResult();

        if ($nurse && $password == $nurse->getPassword()) {
            return $nurse;
        }

        return null;
    }
}
