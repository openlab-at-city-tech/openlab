<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Product\ValueObjects;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents activation metadata for a specific domain.
 *
 * @implements Response<array{
 *     activated_at: string,
 *     deactivated_at: string|null,
 *     is_active: bool,
 *     is_production: bool
 * }>
 */
final class ActivationDomain implements Response
{
	use InteractsWithDateTime;

	public DateTimeImmutable $activatedAt;

	public ?DateTimeImmutable $deactivatedAt;

	public bool $isActive;

	public bool $isProduction;

	private function __construct(
		DateTimeImmutable $activatedAt,
		?DateTimeImmutable $deactivatedAt,
		bool $isActive,
		bool $isProduction
	) {
		$this->activatedAt   = $activatedAt;
		$this->deactivatedAt = $deactivatedAt;
		$this->isActive      = $isActive;
		$this->isProduction  = $isProduction;
	}

	/**
	 * @param array{
	 *     activated_at: string,
	 *     deactivated_at: string|null,
	 *     is_active: bool,
	 *     is_production: bool
	 * } $attributes
	 *
	 * @throws UnexpectedResponseException
	 */
	public static function from(array $attributes): self {
		return new self(
			self::parseDateTime($attributes['activated_at']),
			$attributes['deactivated_at'] !== null
				? self::parseDateTime($attributes['deactivated_at'])
				: null,
			$attributes['is_active'],
			$attributes['is_production']
		);
	}

	public function toArray(): array {
		return [
			'activated_at'   => $this->formatDateTime($this->activatedAt),
			'deactivated_at' => $this->deactivatedAt ? $this->formatDateTime($this->deactivatedAt) : null,
			'is_active'      => $this->isActive,
			'is_production'  => $this->isProduction,
		];
	}
}
