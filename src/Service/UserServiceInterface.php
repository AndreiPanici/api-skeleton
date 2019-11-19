<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Search\UserSearch;
use Pagerfanta\Pagerfanta;

interface UserServiceInterface
{
    /**
     * @param User $user
     *
     * @return User
     */
    public function create(User $user): User;

    /**
     * @param User $user
     *
     * @return User
     */
    public function update(User $user): User;

    /**
     * @param User $user
     *
     * @return void
     */
    public function delete(User $user): void;

    /**
     * @param User $user
     *
     * @return void
     */
    public function setUserRole(User $user): void;

    /**
     * @param UserSearch $userSearch
     *
     * @return Pagerfanta
     */
    public function getUserList(UserSearch $userSearch): Pagerfanta;
}
