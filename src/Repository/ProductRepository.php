<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
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

    public function myFindProductPrice($priceMin, $priceMax)
    {
        $priceMin *= 100;
        $priceMax *= 100;

        $queryBuilder = $this->createQueryBuilder('p')
            ->setParameter('priceMin', $priceMin)
            ->setParameter('priceMax', $priceMax)
            ->where('p.price >= :priceMin')
            ->andWhere('p.price <= :priceMax')
            ->orderBy('p.price', 'ASC');

        $query = $queryBuilder->getQuery();
        $result = $query->getResult();

        dd($result);
        return $result;
    }

    public function myFindSearch($search)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->join('p.category', 'c');
        //->addSelect('c')


        if (!empty($search->getString())) {
            $words = explode(' ', $search->getString());
            $i = 0;

            foreach ($words as $word) {
                $queryBuilder->andWhere(
                    'p.name LIKE :name' . $i
                        . ' OR p.description LIKE :name' . $i
                        . ' OR p.subtitle LIKE :name' . $i
                )
                    ->setParameter('name' . $i++, '%' . $word . '%');
            }
        }


        if (count($search->getCategories()) > 0) {
            $queryBuilder->andWhere(('c.id IN (:categories)'))
                ->setParameter('categories', $search->getCategories());
        }


        $query = $queryBuilder->getQuery();
        $result = $query->getResult();

        //dd($query->getDQL());
        return $result;
    }


    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
