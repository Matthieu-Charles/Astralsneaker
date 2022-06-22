<?php

namespace App\Repository;

use App\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderItem>
 *
 * @method OrderItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderItem[]    findAll()
 * @method OrderItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    public function add(OrderItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OrderItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // public function getListOrderItem()
    // {
    //     $order_items = [];
    //     foreach ($this->createQueryBuilder('c')
    //         ->select('c.product_id', 'c.quantity', 'c.price')
    //         ->getQuery()
    //         ->getResult() as $cols) {
    //         $order_items[] = $cols['c.id'];
    //     }

    //     return $order_items;
    // }

//    /**
//     * @return OrderItem[] Returns an array of OrderItem objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OrderItem
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
