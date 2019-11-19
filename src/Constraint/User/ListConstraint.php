<?php declare(strict_types=1);

namespace App\Constraint\User;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class ListConstraint
{

    /**
     * @return Constraint
     */
    public static function create(): Constraint
    {
        return new Collection(
            [
                'order_by' => [
                    new Assert\Optional(),
                ],
                'order_direction' => [
                    new Assert\Optional([
                        new Assert\Choice(['ASC', 'DESC'])
                    ]),

                ],
                'page' => [
                    new Assert\Type(['type'=> 'integer'])
                ],
                'limit' => [new Assert\Type(['type' => 'integer'])]
            ]
        );
    }
}
