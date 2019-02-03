<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Document::class);
    }

    /**
     * @param string $q
     * @param int $perPage
     * @param int $page
     * @param string $sort
     * @return Document[] Returns an array of Document objects
     */
    public function getAll(string $q = null, int $perPage = 10, int $page = 1, string $sort = 'ASC')
    {
        $query = $this->createQueryBuilder('d')
            ->select('d.id, d.name, d.description, d.created');

        if(!is_null($q) && $q != '')
            $query->andWhere('d.name like :q')
                ->setParameter('q', '%'.$q.'%');

        $query->orderBy('d.id', $sort)
            ->setFirstResult($page * $perPage)
            ->setMaxResults($perPage);

        return $query->getQuery()->getResult();
    }

    /**
     * @param string $q
     * @return mixed
     */
    public function getCount(string $q)
    {
        $query = $this->createQueryBuilder('d')
            ->select('count(d.id)');;

        if(!is_null($q) && $q != '')
            $query->andWhere('d.name like :q')
                ->setParameter('q', '%'.$q.'%');

        try {
            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
        }
    }
}
