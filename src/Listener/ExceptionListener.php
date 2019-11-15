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
     * @var string
     */
    const DEV_ENV = 'dev';

    /**
     * @var NormalizerFactory
     */
    private $normalizerFactory;

    /**
     * @var string
     */
    private $env;

    /**
     * ExceptionListener constructor.
     *
     * @param NormalizerFactory $normalizerFactory
     * @param string $env
     */
    public function __construct(NormalizerFactory $normalizerFactory, string $env)
    {
        $this->normalizerFactory = $normalizerFactory;
        $this->env = $env;
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
        } catch (\Throwable $e) {
            $errors = [];

        }

        return new ApiResponse(null, $exception->getMessage(), $errors, $statusCode);
    }
}