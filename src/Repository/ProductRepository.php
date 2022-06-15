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

    public const PAGINATOR_PER_PAGE = 9;

    public function getProductPaginator(int $offset, string $name = null, string $brand = null, string $pricemini = null,  string $pricemaxi = null): Paginator
    {
        $query = $this->createQueryBuilder('c');

        if ($name) {
            $query = $query
                ->andWhere('c.name = :name')
                ->setParameter('name', $name);
        }

        if ($brand) {
            $query = $query
                ->andWhere('c.brand = :brand')
                ->setParameter('brand', $brand);
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
            // ->addorderBy('c.brand')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
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

    // public function getListBrand()
    // {
    //     $brands = [];
    //     foreach ($this->createQueryBuilder('c')
    //         ->select('c.name', 'c.id')
    //         ->distinct(true)
    //         ->orderBy('c.id', 'ASC')
    //         ->getQuery()
    //         ->getResult() as $cols) {
    //         $brands[$cols['id']] = $cols['name'];
    //     }

    //     return $brands;
    // }


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

    // public function getListBrand()
    // {
    //     $brands = [];
    //     foreach ($this->createQueryBuilder('c')
    //         ->select('c.')
    //         ->distinct(true)
    //         ->orderBy('c.year', 'ASC')
    //         ->getQuery()
    //         ->getResult() as $cols) {
    //         $brands[] = $cols['brand'];
    //     }
    //     return $brands;
    // }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
