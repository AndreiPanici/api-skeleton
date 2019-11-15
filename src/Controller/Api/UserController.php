<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Constraint\User\PatchConstraint;
use App\Constraint\User\PostConstraint;
use App\Entity\User;
use App\Http\ApiResponse;
use App\Services\UserServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractBaseController
{

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
        parent::__construct($serializer, $validator);
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
            json_decode($this->serializer->serialize($this->getUser(), self::RESPONSE_FORMAT, ['groups' => 'details']))
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
        $this->validateContentType($request->headers->get('content-type'));
        $errors = $this->validator->validate(json_decode($request->getContent(), true), PatchConstraint::create());

        if (0 < count($errors)) {
            return new ApiResponse(
                null,
                'Invalid data',
                $this->buildValidatorErrors($errors),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        /** @var User $user */
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            self::RESPONSE_FORMAT,
            [
                'object_to_populate' => $this->getUser(),
                'groups' => 'patch'
            ]
        );

        $this->userService->update($user);

        return new ApiResponse(
            json_decode($this->serializer->serialize($user, self::RESPONSE_FORMAT, ['groups' => 'details'])),
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
        $this->validateContentType($request->headers->get('content-type'));
        $errors = $this->validator->validate(json_decode($request->getContent(), true), PostConstraint::create());

        if (0 < count($errors)) {
            return new ApiResponse(
                null,
                'Invalid data',
                $this->buildValidatorErrors($errors),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        /** @var User $user */
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            ['groups' => 'post']
        );

        $this->userService->create($user);

        return new ApiResponse(
            json_decode($this->serializer->serialize($user, self::RESPONSE_FORMAT, ['groups' => 'details']))
        );
    }
}
