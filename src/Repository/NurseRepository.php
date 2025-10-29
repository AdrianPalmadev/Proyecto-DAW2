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
    public function findById(int $id): ?Nurse
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.id = :u')
            ->setParameter('u', $id)
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

    public function create(Nurse $nurse): Nurse
    {
        $em = $this->getEntityManager();
        $em->persist($nurse);
        $em->flush();
        return $nurse;
    }

    public function delete(Nurse $nurse)
    {
        $em = $this->getEntityManager();
        $em->remove($nurse);
        $em->flush();
    }

    public function edit(Nurse $nurse)
    {
        $em = $this->getEntityManager();
        $em->persist($nurse);
        $em->flush();

        return $nurse;
    }
}
