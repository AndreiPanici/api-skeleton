<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Constraint\User\ListConstraint;
use App\Search\UserSearch;
use App\Service\UserServiceInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="api/admin")
 */
class AdminController extends AbstractBaseController
{

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param UserServiceInterface $userService
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserServiceInterface $userService
    ) {
        parent::__construct($serializer, $validator);
        $this->userService = $userService;
    }

    /**
     * @Route("/user/list", name="user_list", methods={"GET"})
     * @param Request $request
     *
     * @return Response
     */
    public function getUsersList(Request $request): Response
    {
        $queryParams = $request->query->all();
        $queryParams['page'] = (int) $request->query->get('page', 1);
        $queryParams['order_direction'] = $request->query->get('order_direction', UserSearch::ORDER_DIRECTION_DESC);
        $queryParams['limit'] = (int)$request->query->get('limit', UserSearch::DEFAULT_RESULTS);

        $errors = $this->validator->validate($queryParams, ListConstraint::create());
        if (0 < count($errors)) {
            return $this->createApiResponse(
                null,
                'Invalid data',
                $this->buildValidatorErrors($errors),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        /** @var UserSearch $userSearch */
        $userSearch = $this->serializer->deserialize(
            json_encode($queryParams),
            UserSearch::class,
            'json'
        );

        $paginator = $this->userService->getUserList($userSearch);

        $users = [];
        foreach ($paginator->getCurrentPageResults() as $result) {
            $users[] = json_decode(
                $this->serializer->serialize($result, self::RESPONSE_FORMAT, ['groups' => 'details'])
            );
        }

        return $this->createApiResponse(
            [
                'total' => $paginator->getNbResults(),
                'current_page' => $paginator->getCurrentPage(),
                'num_pages' => $paginator->getNbPages(),
                'items_per_page' => $paginator->getMaxPerPage(),
                'users' => $users
            ]
        );
    }
}
