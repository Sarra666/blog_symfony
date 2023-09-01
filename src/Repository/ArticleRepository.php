<?php

namespace App\Repository;

use App\Entity\Article;
use App\Search\SearchArticle;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private PaginatorInterface $paginator
        ){
        parent::__construct($registry, Article::class);
    }

    public function save(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllWithTags(bool $actif = false): array
    {
        $query = $this->createQueryBuilder('a')
            ->select('a', 'u', 'c')
            ->leftJoin('a.categories', 'c')
            ->innerJoin('a.user', 'u');

        if ($actif) {
            $query->andWhere('a.actif = :actif')
                ->setParameter('actif', $actif);
        }

        return $query->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findSearchArticle(SearchArticle $search): PaginationInterface
    {
        $query = $this->createQueryBuilder('a')
            ->select('a','u', 'c')
            ->innerJoin('a.user', 'u')
            ->leftJoin('a.categories', 'c')
            ->andWhere('a.actif= true');

        if( !empty($search->getTitle())) {
                $query ->andWhere('a.titre LIKE :title')
                ->setParameter('title', "%{$search->getTitle()}%");
        }
        if(!empty($search->getTags())){
            $query->andWhere('c.id IN (:tags)')
                ->setParameter('tags', $search->getTags());
        }
        if(!empty($search->getAuthors())){
            $query->andWhere('u.id IN (:authors)')
                ->setParameter('authors', $search->getAuthors());
        }

        $query->getQuery();

        return $this->paginator->paginate(
            //Requête DQL à envoyer en BDD
            $query,
            // Numéro de la page
            $search->getPage(),
            // Nb d'éléments par page
            6,
            [
                'defaultSortFieldName' => ['a.createdAt'],
                'defaultSortDirection' => 'desc'
            ]
        );
    }
    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
