<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Http\Factories;

use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\ApiResponseException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\AuthenticationException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\AuthorizationException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\ClientErrorException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\ConflictException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\DecodingException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\NotFoundException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\ServerErrorException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\ValidationException;
use TEC\Common\LiquidWeb\LicensingApiClient\Http\JsonDecoder;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\ErrorResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Creates typed API response exceptions from failed HTTP responses.
 *
 * @phpstan-type ErrorPayload array{
 *     error: array{
 *         code?: mixed,
 *         message?: mixed
 *     }
 * }
 * @phpstan-type WordPressErrorPayload array{
 *     code?: mixed,
 *     message?: mixed,
 *     data?: array{
 *         status?: int
 *     }
 * }
 */
final class ResponseExceptionFactory
{
	private JsonDecoder $jsonDecoder;

	public function __construct(JsonDecoder $jsonDecoder) {
		$this->jsonDecoder = $jsonDecoder;
	}

	/**
	 * Creates the appropriate typed exception for a failed API response.
	 *
	 * @throws UnexpectedResponseException When the response is not an HTTP error.
	 */
	public function make(ResponseInterface $response): ApiResponseException {
		$statusCode = $response->getStatusCode();

		if ($statusCode >= 400 && $statusCode < 500) {
			return $this->buildClientErrorException($response);
		}

		if ($statusCode >= 500) {
			return $this->buildServerErrorException($response);
		}

		throw new UnexpectedResponseException('Unexpected response status code.', $statusCode);
	}

	private function buildClientErrorException(ResponseInterface $response): ClientErrorException {
		$decoded = $this->decodeErrorPayload($response);

		$normalized = $decoded !== null
			? $this->parseApiError($decoded, $response->getStatusCode())
			: null;

		$errorCode = $normalized !== null && $normalized->code !== null
			? $normalized->code
			: $this->defaultErrorCodeForStatus($response->getStatusCode());

		$message = $normalized !== null && $normalized->message !== null
			? $normalized->message
			: ($response->getReasonPhrase() ?: 'Client Error');

		$statusCode = $normalized !== null && $normalized->statusCode !== null
			? $normalized->statusCode
			: $response->getStatusCode();

		switch ($statusCode) {
			case 401:
				return new AuthenticationException(
					$message,
					$response,
					$statusCode,
					$errorCode,
					$decoded
				);
			case 403:
				return new AuthorizationException(
					$message,
					$response,
					$statusCode,
					$errorCode,
					$decoded
				);
			case 409:
				return new ConflictException(
					$message,
					$response,
					$statusCode,
					$errorCode,
					$decoded
				);
			case 404:
				return new NotFoundException(
					$message,
					$response,
					$statusCode,
					$errorCode,
					$decoded
				);
			case 422:
				return new ValidationException(
					$message,
					$response,
					$statusCode,
					$errorCode,
					$decoded
				);
			default:
				return new ClientErrorException(
					$message,
					$response,
					$statusCode,
					$errorCode,
					$decoded
				);
		}
	}

	/**
	 * @return array<array-key, mixed>|null
	 */
	private function decodeErrorPayload(ResponseInterface $response): ?array {
		try {
			return $this->jsonDecoder->decode((string) $response->getBody());
		} catch (DecodingException $exception) {
			return null;
		}
	}

	/**
	 * @param array<array-key, mixed> $payload
	 */
	private function parseApiError(array $payload, int $statusCode): ?ErrorResponse {
		if ($this->isErrorPayload($payload)) {
			return ErrorResponse::from([
				'code'        => $payload['error']['code'] ?? null,
				'message'     => $payload['error']['message'] ?? null,
				'status_code' => $statusCode,
			]);
		}

		if ($this->isWordPressErrorPayload($payload)) {
			$payloadStatus = $payload['data']['status'] ?? null;

			return ErrorResponse::from([
				'code'        => $payload['code'] ?? null,
				'message'     => $payload['message'] ?? null,
				'status_code' => is_int($payloadStatus) ? $payloadStatus : $statusCode,
			]);
		}

		return null;
	}

	/**
	 * @param array<array-key, mixed>       $payload
	 *
	 * @phpstan-assert-if-true ErrorPayload $payload
	 */
	private function isErrorPayload(array $payload): bool {
		if ( ! isset($payload['error']) || ! is_array($payload['error'])) {
			return false;
		}

		return isset($payload['error']['code']) || isset($payload['error']['message']);
	}

	/**
	 * @param array<array-key, mixed>                $payload
	 *
	 * @phpstan-assert-if-true WordPressErrorPayload $payload
	 */
	private function isWordPressErrorPayload(array $payload): bool {
		return ! ( ! isset($payload['code']) && ! isset($payload['message']));
	}

	private function defaultErrorCodeForStatus(int $statusCode): string {
		switch ($statusCode) {
			case 401:
				return 'authentication_error';
			case 403:
				return 'authorization_error';
			case 409:
				return 'conflict_error';
			case 404:
				return 'not_found';
			case 422:
				return 'validation_error';
			default:
				return 'client_error';
		}
	}

	private function buildServerErrorException(ResponseInterface $response): ServerErrorException {
		$decoded    = $this->decodeErrorPayload($response);
		$normalized = $decoded !== null
			? $this->parseApiError($decoded, $response->getStatusCode())
			: null;

		if ($normalized !== null) {
			return new ServerErrorException(
				$normalized->message ?? ($response->getReasonPhrase() ?: 'Server Error'),
				$response,
				$normalized->statusCode ?? $response->getStatusCode(),
				$normalized->code ?? 'server_error',
				$decoded
			);
		}

		$message = $response->getReasonPhrase() ?: 'Server Error';

		return new ServerErrorException(
			$message,
			$response,
			$response->getStatusCode(),
			'server_error'
		);
	}
}
