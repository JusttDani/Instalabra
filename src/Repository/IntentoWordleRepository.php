<?php

namespace App\Repository;

use App\Entity\IntentoWordle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IntentoWordle>
 */
class IntentoWordleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntentoWordle::class);
    }
}
