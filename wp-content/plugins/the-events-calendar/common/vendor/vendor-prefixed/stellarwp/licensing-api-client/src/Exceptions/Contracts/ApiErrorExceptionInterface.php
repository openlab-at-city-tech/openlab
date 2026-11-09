<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\Contracts;

/**
 * Marks response exceptions that were normalized from a known API error shape.
 */
interface ApiErrorExceptionInterface extends ResponseExceptionInterface
{
	/**
	 * Returns the normalized API error code.
	 */
	public function errorCode(): string;

	/**
	 * Returns the decoded error payload when the response body matched a known JSON error shape.
	 *
	 * @return array<array-key, mixed>|null
	 */
	public function errorPayload(): ?array;
}
