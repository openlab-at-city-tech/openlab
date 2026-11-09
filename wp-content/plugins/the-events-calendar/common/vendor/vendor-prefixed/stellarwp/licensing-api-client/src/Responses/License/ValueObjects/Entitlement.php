<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\ValueObjects;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\ValueObjects\CapabilityCollection;

/**
 * Represents the matched entitlement details for a validated product.
 *
 * @implements Response<array{
 *     tier: string,
 *     site_limit: int,
 *     active_count: int,
 *     available: int,
 *     over_limit: bool,
 *     excess_activations: int,
 *     expiration_date: string,
 *     status: string,
 *     capabilities: list<string>
 * }>
 */
final class Entitlement implements Response
{
	use InteractsWithDateTime;

	public string $tier;

	public int $siteLimit;

	public int $activeCount;

	public int $available;

	public bool $overLimit;

	public int $excessActivations;

	public DateTimeImmutable $expirationDate;

	public string $status;

	public CapabilityCollection $capabilities;

	private function __construct(
		string $tier,
		int $siteLimit,
		int $activeCount,
		int $available,
		bool $overLimit,
		int $excessActivations,
		DateTimeImmutable $expirationDate,
		string $status,
		CapabilityCollection $capabilities
	) {
		$this->tier              = $tier;
		$this->siteLimit         = $siteLimit;
		$this->activeCount       = $activeCount;
		$this->available         = $available;
		$this->overLimit         = $overLimit;
		$this->excessActivations = $excessActivations;
		$this->expirationDate    = $expirationDate;
		$this->status            = $status;
		$this->capabilities      = $capabilities;
	}

	/**
	 * @param array{
	 *     tier: string,
	 *     site_limit: int,
	 *     active_count: int,
	 *     available: int,
	 *     over_limit: bool,
	 *     excess_activations: int,
	 *     expiration_date: string,
	 *     status: string,
	 *     capabilities: list<string>
	 * } $attributes
	 *
	 * @throws UnexpectedResponseException
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['tier'],
			$attributes['site_limit'],
			$attributes['active_count'],
			$attributes['available'],
			$attributes['over_limit'],
			$attributes['excess_activations'],
			self::parseDateTime($attributes['expiration_date']),
			$attributes['status'],
			CapabilityCollection::from($attributes['capabilities'])
		);
	}

	public function toArray(): array {
		return [
			'tier'               => $this->tier,
			'site_limit'         => $this->siteLimit,
			'active_count'       => $this->activeCount,
			'available'          => $this->available,
			'over_limit'         => $this->overLimit,
			'excess_activations' => $this->excessActivations,
			'expiration_date'    => $this->formatDateTime($this->expirationDate),
			'status'             => $this->status,
			'capabilities'       => $this->capabilities->toArray(),
		];
	}
}
