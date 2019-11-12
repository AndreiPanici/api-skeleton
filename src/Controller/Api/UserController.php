<?php declare(strict_types=1);

namespace App\Controller;

use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController
{

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     */
    public function __construct(ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/api/home", name="home", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the rewards of an user",)
     * )
     * @SWG\Tag(name="User")
     * @Security(name="Bearer")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return new JsonResponse('hi');
    }
}
