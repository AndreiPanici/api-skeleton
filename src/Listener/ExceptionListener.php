<?php declare(strict_types=1);

namespace App\Listener;

use App\Factory\NormalizerFactory;
use App\Http\ApiResponse;
use Psr\Log\LoggerInterface;
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param NormalizerFactory $normalizerFactory
     * @param string $env
     * @param LoggerInterface $logger
     */
    public function __construct(NormalizerFactory $normalizerFactory, string $env, LoggerInterface $logger)
    {
        $this->normalizerFactory = $normalizerFactory;
        $this->env = $env;
        $this->logger = $logger;
    }

    /**
     * @param ExceptionEvent $event
     * @throws \Throwable
     *
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
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
     * @throws \Throwable
     *
     * @return ApiResponse
     */
    private function createApiResponse(\Exception $exception)
    {
        $normalizer = $this->normalizerFactory->getNormalizer($exception);
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        try {
            $errors = $normalizer ? $normalizer->normalize($exception) : [];
        } catch (\Throwable $e) {
            $errors = [];
            $this->logger->error($exception);
        }

        return new ApiResponse(null, $exception->getMessage(), $errors, $statusCode);
    }
}
