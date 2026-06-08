<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use RebelCode\Psr7\Message;
use RebelCode\WordPress\Http\Middleware\HttpErrorsToExceptions;
use Throwable;

/**
 * An exception that is thrown by the {@link HttpErrorsToExceptions} middleware for 4xx and 5xx responses.
 */
class BadResponseException extends ResponseException
{
    /**
     * Creates a bad response exception instance based on the status code of a response.
     *
     * @param RequestInterface  $request  The request that was dispatched to retrieve the response.
     * @param ResponseInterface $response The response.
     * @param Throwable|null    $previous Optional previous exception.
     *
     * @return BadResponseException An appropriately created exception:
     *                              - a {@link ClientErrorException} for 4xx responses
     *                              - a {@link ServerErrorException} for 5xx responses
     *                              - a {@link BadResponseException} for other failed responses
     */
    public static function create(
        RequestInterface $request,
        ResponseInterface $response,
        ?Throwable $previous = null
    ): BadResponseException {
        $uri = $request->getUri();
        $uri = static::obfuscateUri($uri);

        // Example:
        // `GET /` resulted in a `404 Not Found` response:
        // <html> ... (truncated)
        $message = sprintf(
            '`%s %s` resulted in a `%s %s` response',
            $request->getMethod(),
            (string) $uri,
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        $summary = Message::bodySummary($response);

        if ($summary !== null) {
            $message .= ":\n{$summary}\n";
        }

        $code = $response->getStatusCode();
        $category = (int) floor($code / 100);

        if ($category === 4) {
            return new ClientErrorException($request, $response, "Client error: $message", $previous);
        } elseif ($category === 5) {
            return new ServerErrorException($request, $response, "Server error: $message", $previous);
        }

        return new self($request, $response, "Unsuccessful request: $message", $previous);
    }

    /**
     * Obfuscates URI if there is a username and a password present.
     *
     * @param UriInterface $uri The URI to obfuscate.
     *
     * @return UriInterface The obfuscated URI.
     */
    protected static function obfuscateUri(UriInterface $uri): UriInterface
    {
        $userInfo = $uri->getUserInfo();
        $colonPos = strpos($userInfo, ':');

        if ($colonPos !== false) {
            $user = substr($userInfo, 0, $colonPos);
            $pass = '***';

            return $uri->withUserInfo($user, $pass);
        }

        return $uri;
    }
}
