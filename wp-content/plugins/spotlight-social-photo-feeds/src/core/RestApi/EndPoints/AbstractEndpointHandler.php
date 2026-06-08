<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints;

use Dhii\Transformer\TransformerInterface;
use Exception;
use InvalidArgumentException;
use RebelCode\Spotlight\Instagram\RestApi\Transformers\CamelCaseTransformer;
use RebelCode\Spotlight\Instagram\RestApi\Transformers\CompositeTransformer;
use RebelCode\Spotlight\Instagram\RestApi\Transformers\RecursiveToArrayTransformer;
use RebelCode\Spotlight\Instagram\Wp\RestRequest;
use Throwable;
use Traversable;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Abstract functionality for REST API endpoint handlers.
 *
 * @since 0.1
 */
abstract class AbstractEndpointHandler
{
    /**
     * Whether or not the response has been sent.
     *
     * @var bool
     */
    protected $responseSent;

    /**
     * The previous exception handler.
     *
     * @var callable|null
     */
    protected $prevExceptionHandler;

    /**
     * @since 0.1
     */
    public function __invoke()
    {
        $this->responseSent = false;
        $this->registerErrorHandler();

        $request = func_get_arg(0);

        if (!($request instanceof WP_REST_Request)) {
            throw new InvalidArgumentException('Argument is not a WP_REST_Request instance');
        }

        try {
            // Handle the request and get the response
            $response = $this->handle($request);

            if ($response instanceof WP_Error) {
                return $response;
            }

            // Retrieve the data
            $data = $response->get_data();
            // Turn the data into an array if it's a traversable
            $aData = ($data instanceof Traversable)
                ? iterator_to_array($data)
                : $data;

            // Transform the data if a transformer is given
            $transformer = $this->getTransformer();
            $tData = ($transformer instanceof TransformerInterface)
                ? $transformer->transform($aData)
                : $aData;

            // Only include fields that were requested
            $fData = RestRequest::has_param($request, 'fields')
                ? array_intersect_key($tData, array_flip($request->get_param('fields')))
                : $tData;

            // Update the response with the final data
            $response->set_data($fData);
            $this->responseSent = true;
        } catch (Exception $exception) {
            $this->sendError(
                $exception->getCode(),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );
        }

        $this->unregisterErrorHandler();

        return $response;
    }

    /**
     * Retrieves the response transformer to use, if any.
     *
     * @since 0.1
     *
     * @return TransformerInterface|null The transformer instance or null if no transformer is required.
     */
    protected function getTransformer()
    {
        return new CompositeTransformer([
            new RecursiveToArrayTransformer(),
            new CamelCaseTransformer(),
        ]);
    }

    /**
     * Creates an erroneous response from an exception.
     *
     * @since 0.1
     *
     * @param Exception $exception The exception from which to create the response.
     *
     * @return WP_Error The erroneous response.
     */
    protected function exceptionResponse(Exception $exception)
    {
        $message = $exception->getMessage();
        $data = [
            'status' => 500,
            'trace' => [],
        ];

        foreach ($exception->getTrace() as $trace) {
            $file = array_key_exists('file', $trace) ? basename($trace['file']) : '<unknown>';
            $line = array_key_exists('line', $trace) ? $trace['line'] : '<unknown>';
            $fn = array_key_exists('function', $trace) ? $trace['function'] : '<unknown>';

            if (array_key_exists('args', $trace)) {
                $args = array_map(function ($arg) {
                    if (is_scalar($arg)) {
                        return $arg;
                    }

                    return is_object($arg)
                        ? get_class($arg)
                        : gettype($arg);
                }, $trace['args']);
                $argsStr = implode(', ', $args);
            } else {
                $argsStr = '<unknown>';
            }

            $data['trace'][] = sprintf('%s(%s) @ %s:%s', $fn, $argsStr, $file, $line);
        }

        return new WP_Error('rest_api_error', $message, $data);
    }

    /**
     * Registers the error handler that sends an erroneous response when an uncaught error is encountered.
     *
     * @since 0.2.3
     */
    protected function registerErrorHandler()
    {
        // Turn off WordPress' fatal error handler
        if (!defined('WP_SANDBOX_SCRAPING')) {
            define('WP_SANDBOX_SCRAPING', true);
        }

        // If we can't turn it off, do not register the error handler
        if (!WP_SANDBOX_SCRAPING) {
            return;
        }

        // This turns off handling for errors that aren't explicitly set to be handled by WordPress
        add_filter('wp_should_handle_php_error', '__return_false');

        // Register our own shutdown function to handle errors
        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error === null) {
                return;
            }

            $this->sendError($error['type'], $error['message'], $error['file'], $error['line']);
        });

        // Register the exception handler
        $this->prevExceptionHandler = set_exception_handler(function (Throwable $exception) {
            $this->sendError(
                $exception->getCode(),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );
        });
    }

    /**
     * Unregisters the error handlers.
     *
     * @since 0.2.3
     */
    protected function unregisterErrorHandler()
    {
        remove_filter('wp_should_handle_php_error', '__return_false');

        if (is_callable($this->prevExceptionHandler)) {
            set_exception_handler($this->prevExceptionHandler);
        }
    }

    /**
     * Sends an erroneous response and terminates execution.
     *
     * @since 0.2.3
     *
     * @param int|string $code The error code.
     * @param string $message The error message.
     * @param string $file
     * @param int $line
     *
     * @return never-returns
     */
    protected function sendError($code, $message = '', $file = '', $line = 0)
    {
        if ($this->responseSent) {
            return;
        }

        // Detect memory exhaustion errors and suggest clearing the cache
        if (
            preg_match('/Allowed memory size of ([0-9]+) bytes exhausted/', $message, $matches) === 1 ||
            preg_match('/Out of memory \(allocated ([0-9]+)/', $message, $matches) === 1
        ) {
            $bytes = (int) $matches[1];
            $megaBytes = ($bytes / 1024.0) / 1024.0;
            $message = "Exhausted {$megaBytes}Mb of memory. To fix this problem, kindly clear the cache from the \"Instagram feeds > Settings > Tools\" page and then try again.";
        }

        $lines = explode("\n", $message);

        http_response_code(500);
        header('Content-type: application/json');

        echo json_encode([
            'code' => $code,
            'message' => $lines[0],
            'details' => $message,
            'file' => $file,
            'line' => $line,
        ]);

        die;
    }

    /**
     * Handles the request and provides a response.
     *
     * @since 0.1
     *
     * @param WP_REST_Request $request The request.
     *
     * @return WP_REST_Response|WP_Error The response or error.
     */
    abstract protected function handle(WP_REST_Request $request);
}
