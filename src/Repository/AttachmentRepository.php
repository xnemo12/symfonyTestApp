<?php

namespace App\Repository;

use App\Entity\Attachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Attachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attachment[]    findAll()
 * @method Attachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttachmentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Attachment::class);
    }

    /**
     * @param $document
     * @return Attachment[] Returns an array of Attachment objects
     */

    public function findByDocument($document)
    {
        return $this->createQueryBuilder('a')
            ->select('a.id, a.originalName, a.position, a.size')
            ->andWhere('a.document = :document')
            ->setParameter('document', $document)
            ->orderBy('a.position', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
}
