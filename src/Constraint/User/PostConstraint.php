<?php declare(strict_types=1);

namespace App\Constraint\User;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

class PostConstraint
{
    /**
     * @return Constraint
     */
    public static function create(): Constraint
    {
        return new Collection(
            [
                'email' => [
                    new Assert\Email()
                ],
                'password' => [
                    new Assert\Type(['type'=> 'string']),
                ]
            ]
        );
    }
}
