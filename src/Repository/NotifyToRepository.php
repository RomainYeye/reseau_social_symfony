<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\NotifyTo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotifyTo|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotifyTo|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotifyTo[]    findAll()
 * @method NotifyTo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method NotifyTo[] searchUserNotifsOfType(User $user, string $type)
 */
class NotifyToRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotifyTo::class);
    }

    // /**
    //  * @return NotifyTo[] Returns an array of NotifyTo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NotifyTo
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    
    public function searchUserNotifsOfType(User $user, string $type, int $flag)
    {
        
        $entityManager = $this->getEntityManager();
    
        $query = $entityManager->createQuery(
            "SELECT nt
            FROM App\Entity\NotifyTo nt INNER JOIN nt.notification noti
            WHERE nt.friend = :user
            AND nt.flag = :flag
            AND noti.type = :typeofnot
            ORDER BY noti.date desc"
        )->setParameters(array('user' => $user, 'flag' => $flag, 'typeofnot' => $type));
    
        return $query->getResult();
    }
    
}
