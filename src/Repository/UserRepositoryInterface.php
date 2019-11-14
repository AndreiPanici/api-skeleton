<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

interface UserRepositoryInterface
{
    /**
     * @param object $entity
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @return object
     */
    public function save($entity);

    /**
     * @param object $entity
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @return void
     */
    public function remove($entity): void;
}
