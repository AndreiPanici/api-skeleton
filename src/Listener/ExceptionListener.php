<?php

namespace App\Listener;

use App\Factory\NormalizerFactory;
use App\Http\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    /**
     * @var NormalizerFactory
     */
    private $normalizerFactory;

    /**
     * ExceptionListener constructor.
     *
     * @param NormalizerFactory $normalizerFactory
     */
    public function __construct(NormalizerFactory $normalizerFactory)
    {
        $this->normalizerFactory = $normalizerFactory;
    }

    /**
     * @param ExceptionEvent $event
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getException();
        $request   = $event->getRequest();

        if (in_array('application/json', $request->getAcceptableContentTypes())) {
            $response = $this->createApiResponse($exception);
            $event->setResponse($response);
        }
    }

    /**
     * @param \Exception $exception
     * @return ApiResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    private function createApiResponse(\Exception $exception)
    {
        $normalizer = $this->normalizerFactory->getNormalizer($exception);
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;

        try {
            $errors = $normalizer ? $normalizer->normalize($exception) : [];
        } catch (\Exception $e) {
            $errors = [];
        }

        return new ApiResponse(null, $exception->getMessage(), $errors, $statusCode);
    }
}