<?php

namespace App\Repository;

use App\Entity\Palabra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Palabra>
 */
class PalabraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Palabra::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.usuario', 'u')
            ->andWhere('u.isBlocked = :blocked OR u.isBlocked IS NULL')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('blocked', false)
            ->orderBy('p.fechaCreacion', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.deletedAt IS NULL')
            ->orderBy('p.fechaCreacion', 'DESC') // DESC = más reciente primero
            ->getQuery()
            ->getResult();
    }
    public function findTopByLikes(int $limit = 5, \DateTimeInterface $startDate = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p AS palabraEntity, COUNT(v.id) AS likesCount') // Seleccionamos la entidad y contamos likes
            ->where('p.deletedAt IS NULL')
            ->leftJoin('p.valoraciones', 'v', 'WITH', 'v.likeActiva = true' . ($startDate ? ' AND v.fechaCreacion >= :startDate' : ''))
            ->groupBy('p.id')
            ->orderBy('likesCount', 'DESC');

        if ($startDate) {
            $qb->setParameter('startDate', $startDate);
        }

        return $qb->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
