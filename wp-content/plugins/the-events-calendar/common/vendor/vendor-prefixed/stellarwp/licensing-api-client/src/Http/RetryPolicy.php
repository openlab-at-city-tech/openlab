<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Http;

/**
 * Defines how transport-level retries should behave for a request.
 */
final class RetryPolicy
{
	public int $maxRetries;

	public int $delayMilliseconds;

	public function __construct(int $maxRetries = 0, int $delayMilliseconds = 0) {
		$this->maxRetries        = $maxRetries;
		$this->delayMilliseconds = $delayMilliseconds;
	}

	public static function default(): self {
		return new self();
	}
}
