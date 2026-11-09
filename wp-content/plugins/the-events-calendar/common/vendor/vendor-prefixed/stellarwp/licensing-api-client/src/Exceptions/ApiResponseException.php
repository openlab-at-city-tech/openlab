<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Exceptions;

use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\Contracts\ApiErrorExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Base exception for HTTP error responses returned by the licensing API.
 */
class ApiResponseException extends RuntimeException implements ApiErrorExceptionInterface
{
	private ResponseInterface $response;

	private int $statusCode;

	private string $errorCode;

	/**
	 * @var array<array-key, mixed>|null
	 */
	private ?array $errorPayload;

	/**
	 * @param array<array-key, mixed>|null $errorPayload
	 */
	public function __construct(
		string $message,
		ResponseInterface $response,
		int $statusCode,
		string $errorCode,
		?array $errorPayload = null
	) {
		parent::__construct($message, $statusCode);

		$this->response     = $response;
		$this->statusCode   = $statusCode;
		$this->errorCode    = $errorCode;
		$this->errorPayload = $errorPayload;
	}

	/**
	 * Returns the raw PSR-7 response that triggered the exception.
	 */
	public function getResponse(): ResponseInterface {
		return $this->response;
	}

	/**
	 * Returns the HTTP status code from the failed response.
	 */
	public function statusCode(): int {
		return $this->statusCode;
	}

	/**
	 * Returns the normalized API error code.
	 */
	public function errorCode(): string {
		return $this->errorCode;
	}

	/**
	 * Returns the decoded error payload when the response body matched a known JSON error shape.
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function errorPayload(): ?array {
		return $this->errorPayload;
	}
}
