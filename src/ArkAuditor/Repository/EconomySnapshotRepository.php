<?php

declare(strict_types=1);

namespace App\ArkAuditor\Repository;

use App\ArkAuditor\Entity\EconomySnapshot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EconomySnapshot>
 */
class EconomySnapshotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EconomySnapshot::class);
    }

    public function findLatest(int $limit = 10): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.auditDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByDateRange(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.auditDate BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('e.auditDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
