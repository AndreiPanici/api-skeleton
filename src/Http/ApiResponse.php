<?php declare(strict_types=1);

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    /**
     * @param mixed  $data
     * @param string $message
     * @param array  $errors
     * @param int    $status
     * @param array  $headers
     * @param bool   $json
     */
    public function __construct(
        $data = null,
        $message = null,
        array $errors = [],
        int $status = 200,
        array $headers = [],
        bool $json = false
    ) {
        parent::__construct($this->format($message, $data, $errors), $status, $headers, $json);
    }

    /**
     * Format the API response.
     *
     * @param string $message
     * @param mixed  $data
     * @param array  $errors
     *
     * @@return mixed
     */
    private function format(string $message = null, $data = null, array $errors = [])
    {
        $response = [];

        if ($data !== null) {
            return $data;
        }

        if ($message) {
            $response['message'] = $message;
        }

        if ($errors) {
            $response['errors'] = $errors;
        }

        return $response;
    }
}
