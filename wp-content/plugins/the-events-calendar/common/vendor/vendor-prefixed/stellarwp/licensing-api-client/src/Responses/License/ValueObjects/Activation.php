<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\ValueObjects;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents an active site activation returned during validation.
 *
 * @implements Response<array{domain: string, activated_at: string}>
 */
final class Activation implements Response
{
	use InteractsWithDateTime;

	public string $domain;

	public DateTimeImmutable $activatedAt;

	private function __construct(string $domain, DateTimeImmutable $activatedAt) {
		$this->domain      = $domain;
		$this->activatedAt = $activatedAt;
	}

	/**
	 * @param array{domain: string, activated_at: string} $attributes
	 *
	 * @throws UnexpectedResponseException
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['domain'],
			self::parseDateTime($attributes['activated_at'])
		);
	}

	public function toArray(): array {
		return [
			'domain'       => $this->domain,
			'activated_at' => $this->formatDateTime($this->activatedAt),
		];
	}
}
