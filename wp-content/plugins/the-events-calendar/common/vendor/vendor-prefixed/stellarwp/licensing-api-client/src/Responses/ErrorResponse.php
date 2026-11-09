<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents a standard API business error response.
 *
 * @implements Response<array{
 *     code?: string|null,
 *     message?: string|null,
 *     status_code?: int|null,
 *     statusCode?: int|null
 * }>
 */
final class ErrorResponse implements Response
{
	public ?string $code;

	public ?string $message;

	public ?int $statusCode;

	private function __construct(
		?string $code,
		?string $message,
		?int $statusCode
	) {
		$this->code       = $code;
		$this->message    = $message;
		$this->statusCode = $statusCode;
	}

	/**
	 * @param array{
	 *     code?: string|null,
	 *     message?: string|null,
	 *     status_code?: int|null,
	 *     statusCode?: int|null
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['code'] ?? null,
			$attributes['message'] ?? null,
			$attributes['statusCode'] ?? $attributes['status_code'] ?? null
		);
	}

	public function toArray(): array {
		return [
			'code'        => $this->code,
			'message'     => $this->message,
			'status_code' => $this->statusCode,
		];
	}
}
