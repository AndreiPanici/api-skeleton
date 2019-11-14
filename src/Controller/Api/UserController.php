<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Http\ApiResponse;
use App\Services\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
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
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @param UserServiceInterface $userService
     */
    public function __construct(
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        UserServiceInterface $userService
    ) {
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->userService = $userService;
    }

    /**
     * @Route("/api/user", name="get_user", methods={"GET"})
     *
     * @return Response
     */
    public function actionGet(): Response
    {
        return new ApiResponse(
            json_decode($this->serializer->serialize($this->getUser(), 'json', ['groups' => 'details']))
        );
    }

    /**
     * @Route("/api/user", name="edit_user", methods={"PATCH"})
     * @param Request $request
     *
     * @return Response
     */
    public function actionPatch(Request $request): Response
    {
        /** @var User $user */
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            [
                'object_to_populate' => $this->getUser(),
                'groups' => 'patch'
            ]
        );

        $errors = $this->validator->validate($user);

        $this->userService->update($user);

        return new ApiResponse(
            json_decode($this->serializer->serialize($user, 'json', ['groups' => 'details'])),
            null,
            []
        );
    }

    /**
     * @Route("/api/user", name="delete_user", methods={"DELETE"})
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request): Response
    {
        $this->userService->delete($this->getUser());

        return new ApiResponse(
            null,
            null,
            [],
            Response::HTTP_NO_CONTENT
        );
    }

    /**
     * @Route("/api/register", name="register_user", methods={"POST"})
     * @param Request $request
     *
     * @return Response
     */
    public function actionPost(Request $request): Response
    {
        /** @var User $user */
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            ['groups' => 'post']
        );

        //TODO return if errors
        $errors = $this->validator->validate($user);

        $this->userService->create($user);

        return new ApiResponse(
            json_decode($this->serializer->serialize($user, 'json', ['groups' => 'details']))
        );
    }
}
