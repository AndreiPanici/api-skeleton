<?php declare(strict_types=1);

namespace App\Repository;

interface UserRepositoryInterface
{
    /**
     * @param object $entity
     * @return object
     */
    public function save($entity);

    /**
     * @param object $entity
     *
     * @return void
     */
    public function remove($entity): void;
}
