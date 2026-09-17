<?php

namespace Symbolist\Exceptions;

use RuntimeException;

class ApiException extends RuntimeException
{
    /** @var int */
    private $statusCode;

    /** @var mixed */
    private $responseBody;

    /**
     * @param mixed $responseBody
     */
    public function __construct(string $message, int $statusCode = 0, $responseBody = null)
    {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
        $this->responseBody = $responseBody;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /** @return mixed */
    public function getResponseBody()
    {
        return $this->responseBody;
    }
}
