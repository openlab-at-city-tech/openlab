<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Credit\ValueObjects;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents one pool entry in a credit balance response.
 *
 * @implements Response<array{
 *     pool_id: int,
 *     pool_remaining: int,
 *     priority: int,
 *     period: string,
 *     resets_on: string|null,
 *     expires_at: string|null,
 *     credits_total?: int,
 *     credits_used?: int,
 *     overage?: int,
 *     overage_limit?: int|null
 * }>
 */
final class PoolBalance implements Response
{
	use InteractsWithDateTime;

	public int $poolId;

	public int $poolRemaining;

	public int $priority;

	public string $period;

	public ?DateTimeImmutable $resetsOn;

	public ?DateTimeImmutable $expiresAt;

	public ?int $creditsTotal;

	public ?int $creditsUsed;

	public ?int $overage;

	public ?int $overageLimit;

	private function __construct(
		int $poolId,
		int $poolRemaining,
		int $priority,
		string $period,
		?DateTimeImmutable $resetsOn,
		?DateTimeImmutable $expiresAt,
		?int $creditsTotal = null,
		?int $creditsUsed = null,
		?int $overage = null,
		?int $overageLimit = null
	) {
		$this->poolId        = $poolId;
		$this->poolRemaining = $poolRemaining;
		$this->priority      = $priority;
		$this->period        = $period;
		$this->resetsOn      = $resetsOn;
		$this->expiresAt     = $expiresAt;
		$this->creditsTotal  = $creditsTotal;
		$this->creditsUsed   = $creditsUsed;
		$this->overage       = $overage;
		$this->overageLimit  = $overageLimit;
	}

	/**
	 * @param array{
	 *     pool_id: int,
	 *     pool_remaining: int,
	 *     priority: int,
	 *     period: string,
	 *     resets_on: string|null,
	 *     expires_at: string|null,
	 *     credits_total?: int,
	 *     credits_used?: int,
	 *     overage?: int,
	 *     overage_limit?: int|null
	 * } $attributes
	 *
	 * @throws UnexpectedResponseException
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['pool_id'],
			$attributes['pool_remaining'],
			$attributes['priority'],
			$attributes['period'],
			self::parseNullableDateTime($attributes['resets_on']),
			self::parseNullableDateTime($attributes['expires_at']),
			$attributes['credits_total'] ?? null,
			$attributes['credits_used'] ?? null,
			$attributes['overage'] ?? null,
			$attributes['overage_limit'] ?? null
		);
	}

	public function toArray(): array {
		$data = [
			'pool_id'        => $this->poolId,
			'pool_remaining' => $this->poolRemaining,
			'priority'       => $this->priority,
			'period'         => $this->period,
			'resets_on'      => $this->resetsOn ? $this->formatDateTime($this->resetsOn) : null,
			'expires_at'     => $this->expiresAt ? $this->formatDateTime($this->expiresAt) : null,
		];

		$data = array_merge($data, array_filter([
			'credits_total' => $this->creditsTotal,
			'credits_used'  => $this->creditsUsed,
			'overage'       => $this->overage,
		], static fn ($value): bool => $value !== null));

		if ($this->overageLimit !== null || array_key_exists('overage', $data)) {
			$data['overage_limit'] = $this->overageLimit;
		}

		return $data;
	}
}
