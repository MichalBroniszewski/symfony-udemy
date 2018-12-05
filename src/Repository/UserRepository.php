<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return mixed
     */
    public function findAllWithMoreThanFivePosts()
    {
        $query = $this->findAllWithMoreThanFivePostsQuery()->getQuery();
        return $query->getResult();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function findAllWithMoreThanFivePostsExceptUser(User $user)
    {
        $query = $this->findAllWithMoreThanFivePostsQuery()
            ->andHaving('u != :user')
            ->setParameter('user', $user)
            ->getQuery();

        return $query->getResult();
    }

    private function findAllWithMoreThanFivePostsQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('u');
        return $qb->select('u')
            ->innerJoin('u.posts', 'mp')
            ->groupBy('u')
            ->having('count(mp) > 5');
    }
}
