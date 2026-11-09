<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Credit\ValueObjects;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents one site quota entry.
 *
 * @implements Response<array{
 *     domain: string,
 *     credit_type: string,
 *     quota: ?int,
 *     period: string,
 *     first_period_start: ?string,
 *     is_blocked: bool,
 *     is_uncapped: bool
 * }>
 */
final class SiteQuota implements Response
{
	use InteractsWithDateTime;

	public string $domain;

	public string $creditType;

	public ?int $quota;

	public string $period;

	public ?DateTimeImmutable $firstPeriodStart;

	public bool $isBlocked;

	public bool $isUncapped;

	private function __construct(
		string $domain,
		string $creditType,
		?int $quota,
		string $period,
		?DateTimeImmutable $firstPeriodStart,
		bool $isBlocked,
		bool $isUncapped
	) {
		$this->domain           = $domain;
		$this->creditType       = $creditType;
		$this->quota            = $quota;
		$this->period           = $period;
		$this->firstPeriodStart = $firstPeriodStart;
		$this->isBlocked        = $isBlocked;
		$this->isUncapped       = $isUncapped;
	}

	/**
	 * @param array{
	 *     domain: string,
	 *     credit_type: string,
	 *     quota: ?int,
	 *     period: string,
	 *     first_period_start: ?string,
	 *     is_blocked: bool,
	 *     is_uncapped: bool
	 * } $attributes
	 *
	 * @throws UnexpectedResponseException
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['domain'],
			$attributes['credit_type'],
			$attributes['quota'],
			$attributes['period'],
			self::parseNullableDateTime($attributes['first_period_start']),
			$attributes['is_blocked'],
			$attributes['is_uncapped']
		);
	}

	/**
	 * @return array{
	 *     domain: string,
	 *     credit_type: string,
	 *     quota: ?int,
	 *     period: string,
	 *     first_period_start: ?string,
	 *     is_blocked: bool,
	 *     is_uncapped: bool
	 * }
	 */
	public function toArray(): array {
		return [
			'domain'             => $this->domain,
			'credit_type'        => $this->creditType,
			'quota'              => $this->quota,
			'period'             => $this->period,
			'first_period_start' => $this->firstPeriodStart ? $this->formatDateTime($this->firstPeriodStart) : null,
			'is_blocked'         => $this->isBlocked,
			'is_uncapped'        => $this->isUncapped,
		];
	}
}
