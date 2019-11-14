<?php declare(strict_types=1);

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService implements UserServiceInterface
{

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserRepositoryInterface $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
    }

    /**
     * @inheritDoc
     */
    public function create(User $user): User
    {
        $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function update(User $user): User
    {
        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function delete(User $user): void
    {
        $this->userRepository->remove($user);
    }
}