<?php declare(strict_types=1);

namespace App\Constraint\User;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

class PatchConstraint
{

    /**
     * @return Constraint
     */
    static function create(): Constraint
    {
        return new Collection(
            [
                'phone' => [
                        new Assert\Type(['type'=> 'string']),
                        new Assert\Length(['min'=> '8', 'max' => '20']),
                ],
                'first_name' => [
                    new Assert\Type(['type'=> 'string']),
                ],
                'last_name' => [
                    new Assert\Type(['type'=> 'string']),
                ]
            ]
        );
    }
}
