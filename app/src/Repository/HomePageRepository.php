<?php

namespace App\Repository;

use App\Entity\HomePage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HomePage>
 *
 * @method null|HomePage find($id, $lockMode = null, $lockVersion = null)
 * @method null|HomePage findOneBy(array $criteria, array $orderBy = null)
 * @method HomePage[]    findAll()
 * @method HomePage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HomePageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HomePage::class);
    }

    //    /**
    //     * @return HomePage[] Returns an array of HomePage objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?HomePage
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getHomePage(int $id): ?HomePage
    {
        return $this->createQueryBuilder('h')
            ->select('h')
            ->leftJoin('h.websiteContents', 'websiteContents')
            ->addSelect('websiteContents')
            ->leftJoin('websiteContents.picture', 'websiteContentsPicture')
            ->addSelect('websiteContentsPicture')
            ->leftJoin('h.sliders', 'sliders')
            ->addSelect('sliders')
            ->leftJoin('sliders.product', 'slidersProduct')
            ->addSelect('slidersProduct')
            ->leftJoin('slidersProduct.pictures', 'slidersPictures')
            ->addSelect('slidersPictures')
            ->leftJoin('h.underSliders', 'underSliders')
            ->addSelect('underSliders')
            ->leftJoin('underSliders.product', 'underSlidersProduct')
            ->addSelect('underSlidersProduct')
            ->leftJoin('underSlidersProduct.pictures', 'underSlidersPictures')
            ->addSelect('underSlidersPictures')
            ->leftJoin('h.mids', 'mids')
            ->addSelect('mids')
            ->leftJoin('mids.product', 'midsProduct')
            ->addSelect('midsProduct')
            ->leftJoin('midsProduct.pictures', 'midsPictures')
            ->addSelect('midsPictures')
            ->where('h.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
            ->getOneOrNullResult()
        ;
    }
}
