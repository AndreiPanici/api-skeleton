<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }
}
