<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * An exception that is thrown in relation to a request its corresponding response.
 */
class ResponseException extends RequestException
{
    /** @var ResponseInterface */
    protected $response;

    /**
     * @inheritDoc
     *
     * @param ResponseInterface $response The response.
     */
    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        $message = "",
        Throwable $previous = null
    ) {
        parent::__construct($request, $message, $response->getStatusCode(), $previous);

        $this->response = $response;
    }

    /**
     * Retrieves the response that is related to the exception.
     *
     * @return ResponseInterface The response instance.
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
