<?php

namespace App\Repository;

use App\Entity\MicroPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MicroPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method MicroPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method MicroPost[]    findAll()
 * @method MicroPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MicroPostRepository extends ServiceEntityRepository
{
    /**
     * MicroPostRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MicroPost::class);
    }

    /**
     * @param Collection $users
     * @return mixed
     */
    public function findAllByUsers(Collection $users)
    {
        $qb = $this->createQueryBuilder('mp');
        $query = $qb->select('mp')
            ->where('mp.user IN (:following)')
            ->setParameter('following', $users)
            ->orderBy('mp.time', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
}
