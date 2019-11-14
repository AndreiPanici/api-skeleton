<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

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
}