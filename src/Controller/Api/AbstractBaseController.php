<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ValidationException;
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
     * @param string $contentType
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
     */
    protected function validateRequestData(string $data, string $model)
    {
        $this->data = $this->serializer->deserialize($data, $model, self::RESPONSE_FORMAT);

        $errors = $this->validator->validate($this->data);
        if ($errors->count() > 0) {
            throw new ValidationException($this->createErrorMessage($errors), Response::HTTP_BAD_REQUEST);
        }


    }

    /**
     * @param ConstraintViolationListInterface $validatorErrors
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
