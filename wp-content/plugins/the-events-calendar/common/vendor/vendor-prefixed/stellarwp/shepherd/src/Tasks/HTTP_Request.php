<?php

/**
 * Shepherd's HTTP request task.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Tasks;
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd\Tasks;

use TEC\Common\StellarWP\Shepherd\Config;
use TEC\Common\StellarWP\Shepherd\Abstracts\Task_Abstract;
use TEC\Common\StellarWP\Shepherd\Exceptions\ShepherdTaskException;
use TEC\Common\StellarWP\Shepherd\Exceptions\ShepherdTaskFailWithoutRetryException;
use InvalidArgumentException;
use WP_Error;
// phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found
/**
 * Shepherd's HTTP request task.
 *
 * This task makes HTTP requests using WordPress's wp_remote_request() function
 * with built-in retry logic for failed requests.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Tasks;
 */
class HTTP_Request extends Task_Abstract
{
    /**
     * Valid HTTP methods.
     *
     * @since 0.0.1
     *
     * @var string[]
     */
    private const VALID_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];
    /**
     * Default request timeout in seconds.
     *
     * @since 0.0.1
     *
     * @var array
     */
    private const DEFAULT_ARGS = [
        'timeout' => 3,
        'reject_unsafe_urls' => true,
        // Pass the URL(s) through the `wp_http_validate_url()` function.
        'compress' => true,
        // Always compress the request.
        'decompress' => true,
        'redirection' => 5,
    ];
    /**
     * The HTTP request task's constructor.
     *
     * @since 0.0.1
     *
     * @param string $url     The URL to send the request to.
     * @param array  $args    Optional. Request arguments (headers, body, timeout, etc.).
     * @param string $method  Optional. HTTP method (GET, POST, etc.). Default 'GET'.
     *
     * @throws InvalidArgumentException If the HTTP request arguments are invalid.
     */
    public function __construct(string $url, array $args = [], string $method = 'GET')
    {
        parent::__construct($url, $args, strtoupper($method));
    }
    // phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.Missing
    /**
     * Processes the HTTP request task.
     *
     * @since 0.0.1
     *
     * @throws ShepherdTaskException                 If the HTTP request fails but should be retried.
     * @throws ShepherdTaskFailWithoutRetryException If the HTTP request fails without retry.
     */
    public function process(): void
    {
        $url = $this->get_url();
        $method = $this->get_method();
        $request_args = array_merge(self::DEFAULT_ARGS, $this->get_request_args());
        $request_args['method'] = $method;
        if (!(isset($request_args['headers']) && is_array($request_args['headers']))) {
            $request_args['headers'] = [];
        }
        $request_args['headers'] = array_merge($request_args['headers'], $this->get_auth_headers());
        $request_args['headers']['X-Shepherd-Task-ID'] = $this->get_id();
        $response = wp_remote_request($url, $request_args);
        if (is_wp_error($response)) {
            /**
             * Filters whether to retry the HTTP request on WP_Error.
             *
             * @since 0.0.1
             *
             * @param bool         $should_retry Whether to retry the HTTP request on WP_Error.
             * @param WP_Error     $response     The WP_Error object.
             * @param HTTP_Request $task         The HTTP request task.
             */
            $should_retry = apply_filters('shepherd_' . Config::get_hook_prefix() . '_http_request_should_retry_on_wp_error', false, $response, $this);
            $expection_class = $should_retry ? ShepherdTaskException::class : ShepherdTaskFailWithoutRetryException::class;
            throw new $expection_class(sprintf(
                /* translators: %1$s: HTTP method, %2$s: URL, %3$s: Error message */
                __('HTTP %1$s request to %2$s failed with code: `%3$s` and message: `%4$s`', 'stellarwp-shepherd'),
                $method,
                $url,
                $response->get_error_code(),
                $response->get_error_message()
            ));
        }
        if (!is_array($response)) {
            /**
             * Filters whether to retry the HTTP request on invalid response.
             *
             * @since 0.0.1
             *
             * @param bool         $should_retry Whether to retry the HTTP request on invalid response.
             * @param mixed        $response     The response.
             * @param HTTP_Request $task         The HTTP request task.
             */
            $should_retry = apply_filters('shepherd_' . Config::get_hook_prefix() . '_http_request_should_retry_on_invalid_response', false, $response, $this);
            $expection_class = $should_retry ? ShepherdTaskException::class : ShepherdTaskFailWithoutRetryException::class;
            throw new $expection_class(__('HTTP request returned an invalid response.', 'stellarwp-shepherd'));
        }
        $response_code = wp_remote_retrieve_response_code($response);
        $response_message = wp_remote_retrieve_response_message($response);
        // Client errors (4xx) fail immediately, server errors (5xx) are retried.
        if ($response_code >= 400 && $response_code < 500) {
            /**
             * Filters whether to retry the HTTP request on HTTP error status codes (4xx, 5xx).
             *
             * @since 0.0.1
             *
             * @param bool         $should_retry Whether to retry the HTTP request on HTTP error status codes (4xx, 5xx).
             * @param array        $response     The response.
             * @param HTTP_Request $task         The HTTP request task.
             */
            $should_retry = apply_filters('shepherd_' . Config::get_hook_prefix() . '_http_request_should_retry_on_http_error_status_codes', false, $response, $this);
            $expection_class = $should_retry ? ShepherdTaskException::class : ShepherdTaskFailWithoutRetryException::class;
            throw new $expection_class(sprintf(
                /* translators: %1$s: HTTP method, %2$s: URL, %3$d: Response code, %4$s: Response message */
                __('HTTP %1$s request to %2$s returned error %3$d: `%4$s`', 'stellarwp-shepherd'),
                $method,
                $url,
                $response_code,
                $response_message
            ));
        }
        if ($response_code < 200 || $response_code >= 300) {
            /**
             * Filters whether to retry the HTTP request on non-2xx response codes.
             *
             * @since 0.0.1
             *
             * @param bool         $should_retry Whether to retry the HTTP request on non-2xx response codes.
             * @param array        $response     The response.
             * @param HTTP_Request $task         The HTTP request task.
             */
            $should_retry = apply_filters('shepherd_' . Config::get_hook_prefix() . '_http_request_should_retry_on_non_2xx_response_codes', true, $response, $this);
            $expection_class = $should_retry ? ShepherdTaskException::class : ShepherdTaskFailWithoutRetryException::class;
            throw new $expection_class(sprintf(
                /* translators: %1$s: HTTP method, %2$s: URL, %3$d: Response code, %4$s: Response message */
                __('HTTP %1$s request to %2$s returned error %3$d: `%4$s`', 'stellarwp-shepherd'),
                $method,
                $url,
                $response_code,
                $response_message
            ));
        }
        /**
         * Fires when the HTTP request task is processed successfully.
         *
         * @since 0.0.1
         *
         * @param HTTP_Request $task     The HTTP request task that was processed.
         * @param array        $response The wp_remote_request response array.
         */
        do_action('shepherd_' . Config::get_hook_prefix() . '_http_request_processed', $this, $response);
    }
    // phpcs:enable Squiz.Commenting.FunctionCommentThrowTag.Missing
    /**
     * Validates the HTTP request task's arguments.
     *
     * @since 0.0.1
     *
     * @throws InvalidArgumentException If the HTTP request arguments are invalid.
     */
    protected function validate_args(): void
    {
        $args = $this->get_args();
        if (empty($args[0])) {
            throw new InvalidArgumentException(__('URL is required.', 'stellarwp-shepherd'));
        }
        if (isset($args[1]) && !is_array($args[1])) {
            throw new InvalidArgumentException(__('Request arguments must be an array.', 'stellarwp-shepherd'));
        }
        if (isset($args[2]) && !in_array(strtoupper($args[2]), self::VALID_METHODS, true)) {
            throw new InvalidArgumentException(sprintf(
                /* translators: %s: Valid HTTP methods */
                __('HTTP method must be one of: %s', 'stellarwp-shepherd'),
                implode(', ', self::VALID_METHODS)
            ));
        }
    }
    /**
     * Gets the HTTP request task's hook prefix.
     *
     * @since 0.0.1
     *
     * @return string The HTTP request task's hook prefix.
     */
    public function get_task_prefix(): string
    {
        return 'shepherd_http_';
    }
    /**
     * Gets the maximum number of retries.
     *
     * Network requests can be flaky, so allow retries.
     *
     * @since 0.0.1
     *
     * @return int The maximum number of retries.
     */
    public function get_max_retries(): int
    {
        return 10;
    }
    /**
     * Gets the request URL.
     *
     * @since 0.0.1
     *
     * @return string The request URL.
     */
    public function get_url(): string
    {
        return $this->get_args()[0];
    }
    /**
     * Gets the HTTP method.
     *
     * @since 0.0.1
     *
     * @return string The HTTP method.
     */
    public function get_method(): string
    {
        return $this->get_args()[2] ?? 'GET';
    }
    /**
     * Gets the request arguments.
     *
     * @since 0.0.1
     *
     * @return array The request arguments.
     */
    public function get_request_args(): array
    {
        return $this->get_args()[1] ?? [];
    }
    /**
     * Gets the authentication headers.
     *
     * Offers an alternative of having to store the auth credentials in the database.
     *
     * @since 0.0.1
     *
     * @return array The authentication headers.
     */
    public function get_auth_headers(): array
    {
        return [];
    }
}