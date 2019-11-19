<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ValidationException;
use App\Http\ApiResponse;
use Doctrine\Common\Inflector\Inflector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractBaseController extends AbstractController
{

    /**
     * @var string
     */
    const CONTENT_TYPE = 'application/json';

    /**
     * @var string
     */
    const RESPONSE_FORMAT = 'json';

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @param null $data
     * @param null $message
     * @param array $errors
     * @param int $status
     * @param array $headers
     * @param bool $json
     * @return ApiResponse
     */
    protected function createApiResponse(
        $data = null,
        $message = null,
        array $errors = [],
        int $status = 200,
        array $headers = [],
        bool $json = false
    ) {

        return new ApiResponse(
            $data,
            $message,
            $errors,
            $status,
            $headers,
            $json
        );
    }

    /**
     * @param string $contentType
     *
     * @return void
     */
    protected function validateContentType(string $contentType): void
    {
        if (self::CONTENT_TYPE !== $contentType) {
            throw new ValidationException(
                'Invalid content type header.',
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE
            );
        }
    }

    /**
     * @param string $data
     * @param string $model
     *
     * @return void
     */
    protected function validateRequestData(string $data, string $model): void
    {
        $result = $this->serializer->deserialize($data, $model, self::RESPONSE_FORMAT);

        $errors = $this->validator->validate($result);
        if ($errors->count() > 0) {
            throw new ValidationException($this->createErrorMessage($errors), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param ConstraintViolationListInterface $validatorErrors
     *
     * @return array
     */
    protected function buildValidatorErrors(ConstraintViolationListInterface $validatorErrors): array
    {
        $errors = [];

        /** @var ConstraintViolation $validatorError */
        foreach ($validatorErrors as $validatorError) {
            $errors[] = [
                'property' => str_replace(array( '[', ']' ), '', $validatorError->getPropertyPath()),
                'message' => $validatorError->getMessage(),
                'invalid_value' => $validatorError->getInvalidValue()
            ];
        }

        return $errors;
    }

    /**
     * @param ConstraintViolationListInterface $violations
     *
     * @return string
     */
    private function createErrorMessage(ConstraintViolationListInterface $violations): string
    {
        $errors = [];

        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $errors[Inflector::tableize($violation->getPropertyPath())] = $violation->getMessage();
        }

        return json_encode(['errors' => $errors]);
    }
}
