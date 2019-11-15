<?php declare(strict_types=1);

namespace App\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class ApplicationException extends HttpException
{
    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message, int $code, Exception $previous = null)
    {
        parent::__construct($code, $message, $previous);
    }
}
