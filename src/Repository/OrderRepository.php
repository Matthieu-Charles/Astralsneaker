<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function add(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getOrderPaginator(int $paginatorInt, int $offset, string $order_search = null, string $total_mini_search = null,  string $total_maxi_search = null, string $date_mini_search = null,  string $date_maxi_search = null): Paginator
    {
       $query = $this->createQueryBuilder('c');

        if ($order_search) {
            $query = $query
                    ->join('c.user', 'u', 'u.id = c.id')
                    ->orWhere('u.firstName LIKE :order_search')
                    ->orWhere('u.lastName LIKE :order_search')
                    ->orWhere('u.id LIKE :order_search')
                    ->setParameter('order_search', '%'.$order_search.'%');
        }

        if ($total_mini_search > $total_maxi_search) {
            [$total_mini_search, $total_maxi_search] = [$total_maxi_search, $total_mini_search];
        }

        if ($total_mini_search) {
            $query = $query
                ->andWhere('c.total >= :pricemini')
                ->setParameter('pricemini',$total_mini_search);
        }

        if ($total_maxi_search) {
            $query = $query
                ->andWhere('c.total <= :pricemaxi')
                ->setParameter('pricemaxi', $total_maxi_search);
        }

        if ($date_mini_search) {
            $query = $query
                ->andWhere('c.createdAt >= :datemini')
                ->setParameter('datemini',$date_mini_search);
        }

        if ($date_maxi_search) {
            $query = $query
                ->andWhere('c.createdAt <= :datemaxi')
                ->setParameter('datemaxi', $date_maxi_search);
        }

        $query = $query->orderBy('c.id')
            ->setMaxResults($paginatorInt)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }

    public function getOrderInfo(): Paginator
    {
      
       $query = $this->createQueryBuilder('c')
       ->orderBy('c.id', 'ASC')
       ->getQuery();

        return new Paginator($query);
    }
}