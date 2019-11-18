<?php declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use App\Service\UserServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserServiceTest extends TestCase
{

    /**
     * @var UserServiceInterface|MockObject
     */
    private $userService;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->userService = $this->getMockBuilder(UserService::class)
            ->setConstructorArgs(
                [
                    $this->getMockBuilder(UserRepository::class)->disableOriginalConstructor()
                        ->getMock(),
                    $this->getMockBuilder(UserPasswordEncoderInterface::class)->disableOriginalConstructor()
                        ->getMock()
                ]
            )
            ->setMethods()
            ->getMock();
    }

    /**
     * @return void
     */
    public function testSetUserRole(): void
    {
        $user = new User();
        $role = User::USER_ROLE;

        $this->userService->setUserRole($user);
        self::assertContains($role, $user->getRoles());
    }
}
