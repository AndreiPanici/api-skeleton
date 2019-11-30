<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Search\UserSearch;
use Doctrine\Common\Persistence\ManagerRegistry;
use Pagerfanta\Pagerfanta;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param UserSearch $userSearch
     *
     * @return Pagerfanta
     */
    public function search(UserSearch $userSearch): Pagerfanta
    {
        $qb = $this->createQueryBuilder('user')
            ->select('user')
            ->where('user.roles LIKE :role')
            ->setParameter('role', '%"' . User::USER_ROLE . '"%')
            ->orderBy('user.firstName', $userSearch->getOrderDirection());

        if ('lastName' === $userSearch->getOrderBy()) {
            $qb->orderBy('user.lastName', $userSearch->getOrderDirection());
        }

        if ('email' === $userSearch->getOrderBy()) {
            $qb->orderBy('user.email', $userSearch->getOrderDirection());
        }

        return $this->paginate($qb, $userSearch->getLimit(), $userSearch->getPage());
    }
}
