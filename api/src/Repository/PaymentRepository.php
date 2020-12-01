<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function calculateAcountBalance($acount)
    {
        $totals = $this->createQueryBuilder('p')
            //->select('(SUM(p.debit) - SUM(p.credit)) as credit')
            ->select('SUM(p.debit) as debit, SUM(p.credit) as credit')
            ->andWhere('p.acount = :acount')
            ->setParameter('acount', $acount)
            ->getQuery()
            ->getOneOrNullResult();

        // The sums might be null if there are no payments, that will create trouble later on
        if (!$totals['debit']) {
            $totals['debit'] = 0;
        }
        if (!$totals['credit']) {
            $totals['credit'] = 0;
        }

        return $totals['credit'] - $totals['debit'];
    }
}
