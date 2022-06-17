<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public const PAGINATOR_PER_PAGE_1 = 4;

    public const PAGINATOR_PER_PAGE_2 = 9;


    public function getProductPaginator(int $paginatorInt, int $offset, string $name_search = null, $brands = null, string $pricemini = null,  string $pricemaxi = null): Paginator
    {
        $query = $this->createQueryBuilder('c');

        if ($name_search) {
            $query = $query
                    ->andWhere('c.description LIKE :name_search')
                    ->orWhere('c.name LIKE :name_search')
                    ->setParameter('name_search', '%'.$name_search.'%');
        }

        if ($pricemini > $pricemaxi) {
            [$pricemini, $pricemaxi] = [$pricemaxi, $pricemini];
        }

        if ($brands) {
                $query = $query
                    ->andWhere('c.brand in (:brands)')
                    ->setParameter('brands', $brands);
        }

        if ($pricemini) {
            $query = $query
                ->andWhere('c.price >= :pricemini')
                ->setParameter('pricemini', $pricemini);
        }

        if ($pricemaxi) {
            $query = $query
                ->andWhere('c.price <= :pricemaxi')
                ->setParameter('pricemaxi', $pricemaxi);
        }

        $query = $query->orderBy('c.name', 'DESC')
            ->setMaxResults($paginatorInt)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }

    public function getOneProductPaginator(int $paginatorInt, Product $product , int $offset): Paginator
    {
        $query = $this->createQueryBuilder('c')
        ->andWhere('c.id = :product')
        ->setParameter('product', $product)
        ->orderBy('c.name', 'DESC')
        ->setMaxResults($paginatorInt)
        ->setFirstResult($offset)
        ->getQuery();

        return new Paginator($query);
    }

    public function getListName()
    {
        $names = [];
        foreach ($this->createQueryBuilder('c')
            ->select('c.name')
            ->distinct(true)
            ->orderBy('c.brand', 'ASC')
            ->getQuery()
            ->getResult() as $cols) {
            $names[] = $cols['name'];
        }

        return $names;
    }

    public function getListPrice()
    {
        $prices = [];
        foreach ($this->createQueryBuilder('c')
            ->select('c.price')
            ->distinct(true)
            ->orderBy('c.price', 'ASC')
            ->getQuery()
            ->getResult() as $cols) {
            $prices[] = $cols['price'];
        }

        return $prices;
    }
}
