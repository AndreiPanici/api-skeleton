<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Constraint\User\PatchConstraint;
use App\Constraint\User\PostConstraint;
use App\Entity\User;
use App\Service\UserServiceInterface;
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
        return $this->createApiResponse(
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
            return $this->createApiResponse(
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

        return $this->createApiResponse(
            json_decode($this->serializer->serialize($user, self::RESPONSE_FORMAT, ['groups' => 'details'])),
            null,
            []
        );
    }

    /**
     * @Route("/api/user", name="delete_user", methods={"DELETE"})
     *
     * @return Response
     */
    public function deleteAction(): Response
    {
        /** @var User $loggedUser */
        $loggedUser = $this->getUser();
        $this->userService->delete($loggedUser);

        return $this->createApiResponse(
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
            return $this->createApiResponse(
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

        $this->userService->setUserRole($user);
        $this->userService->create($user);

        return $this->createApiResponse(
            json_decode($this->serializer->serialize($user, self::RESPONSE_FORMAT, ['groups' => 'details']))
        );
    }
}
