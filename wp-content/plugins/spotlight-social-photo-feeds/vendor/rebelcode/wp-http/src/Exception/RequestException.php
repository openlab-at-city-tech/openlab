<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Exception;

use Psr\Http\Message\RequestInterface;
use Throwable;

/**
 * An exception that is thrown in relation to a request.
 */
class RequestException extends HttpException
{
    /** @var RequestInterface */
    protected $request;

    /**
     * @inheritDoc
     *
     * @param RequestInterface $request The request.
     */
    public function __construct(RequestInterface $request, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->request = $request;
    }

    /**
     * Retrieves the request that is related to the exception.
     *
     * @return RequestInterface The request instance.
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
