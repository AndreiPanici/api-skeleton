<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

abstract class AbstractRepository extends ServiceEntityRepository
{

    /**
     * @param object $entity
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @return object
     */
    public function save($entity)
    {
        if (null === $entity->getId()) {
            $this->_em->persist($entity);
        }

        $this->_em->flush();

        return $entity;
    }

    /**
     * @param object $entity
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @return void
     */
    public function remove($entity): void
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }

    /**
     * @param QueryBuilder $qb
     * @param int $limit
     * @param int $currentPage
     *
     * @return Pagerfanta
     */
    public function paginate(QueryBuilder $qb, int $limit, int $currentPage): Pagerfanta
    {
        $pager = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }
}
