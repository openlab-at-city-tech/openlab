<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\ValueObjects;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\ValueObjects\CapabilityCollection;

/**
 * Represents an entitlement option when tier selection is required.
 *
 * @implements Response<array{
 *     tier: string,
 *     site_limit: int,
 *     active_count: int,
 *     available: int,
 *     over_limit: bool,
 *     excess_activations: int,
 *     capabilities: list<string>,
 *     status: string,
 *     expires: string
 * }>
 */
final class AvailableEntitlement implements Response
{
	use InteractsWithDateTime;

	public string $tier;

	public int $siteLimit;

	public int $activeCount;

	public int $available;

	public bool $overLimit;

	public int $excessActivations;

	public CapabilityCollection $capabilities;

	public string $status;

	public DateTimeImmutable $expires;

	private function __construct(
		string $tier,
		int $siteLimit,
		int $activeCount,
		int $available,
		bool $overLimit,
		int $excessActivations,
		CapabilityCollection $capabilities,
		string $status,
		DateTimeImmutable $expires
	) {
		$this->tier              = $tier;
		$this->siteLimit         = $siteLimit;
		$this->activeCount       = $activeCount;
		$this->available         = $available;
		$this->overLimit         = $overLimit;
		$this->excessActivations = $excessActivations;
		$this->capabilities      = $capabilities;
		$this->status            = $status;
		$this->expires           = $expires;
	}

	/**
	 * @param array{
	 *     tier: string,
	 *     site_limit: int,
	 *     active_count: int,
	 *     available: int,
	 *     over_limit: bool,
	 *     excess_activations: int,
	 *     capabilities: list<string>,
	 *     status: string,
	 *     expires: string
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
			CapabilityCollection::from($attributes['capabilities']),
			$attributes['status'],
			self::parseDateTime($attributes['expires'])
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
			'capabilities'       => $this->capabilities->toArray(),
			'status'             => $this->status,
			'expires'            => $this->formatDateTime($this->expires),
		];
	}
}
