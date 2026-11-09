<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\Credit;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;

/**
 * Represents a credits ledger query request.
 *
 * @phpstan-type ListLedgerEntriesQuery array{
 *     license_key: string,
 *     domain?: string,
 *     credit_type?: string,
 *     pool_id?: int,
 *     entry_type?: string,
 *     after?: string,
 *     before?: string,
 *     limit: int,
 *     starting_after?: int,
 *     ending_before?: int
 * }
 */
final class ListLedgerEntries
{
	use InteractsWithDateTime;

	/**
	 * License key to query ledger entries for.
	 *
	 * @example LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N
	 */
	public string $licenseKey;

	/**
	 * Restrict results to a specific site domain.
	 *
	 * @example example.com
	 */
	public ?string $domain;

	/**
	 * Restrict results to a single credit type.
	 *
	 * @example ai
	 */
	public ?string $creditType;

	/**
	 * Restrict results to a specific credit pool ID.
	 *
	 * @example 42
	 */
	public ?int $poolId;

	/**
	 * Restrict results to a single ledger entry type.
	 *
	 * @example usage
	 * @example refund
	 */
	public ?string $entryType;

	/**
	 * Return entries created on or after this UTC timestamp.
	 *
	 * @example new DateTimeImmutable('2026-03-01T00:00:00Z')
	 */
	public ?DateTimeImmutable $after;

	/**
	 * Return entries created on or before this UTC timestamp.
	 *
	 * @example new DateTimeImmutable('2026-03-31T23:59:59Z')
	 */
	public ?DateTimeImmutable $before;

	/**
	 * Maximum number of entries to return.
	 *
	 * @example 50
	 */
	public int $limit;

	/**
	 * Cursor for pagination. Return entries with IDs higher than this value.
	 *
	 * @example 1002
	 */
	public ?int $startingAfter;

	/**
	 * Cursor for pagination. Return entries with IDs lower than this value.
	 *
	 * @example 1002
	 */
	public ?int $endingBefore;

	public function __construct(
		string $licenseKey,
		?string $domain = null,
		?string $creditType = null,
		?int $poolId = null,
		?string $entryType = null,
		?DateTimeImmutable $after = null,
		?DateTimeImmutable $before = null,
		int $limit = 50,
		?int $startingAfter = null,
		?int $endingBefore = null
	) {
		$this->licenseKey    = $licenseKey;
		$this->domain        = $domain;
		$this->creditType    = $creditType;
		$this->poolId        = $poolId;
		$this->entryType     = $entryType;
		$this->after         = $after;
		$this->before        = $before;
		$this->limit         = $limit;
		$this->startingAfter = $startingAfter;
		$this->endingBefore  = $endingBefore;
	}

	/**
	 * @return ListLedgerEntriesQuery
	 */
	public function toQuery(): array {
		return array_filter([
			'license_key'    => $this->licenseKey,
			'domain'         => $this->domain,
			'credit_type'    => $this->creditType,
			'pool_id'        => $this->poolId,
			'entry_type'     => $this->entryType,
			'after'          => $this->after ? $this->formatDateTime($this->after) : null,
			'before'         => $this->before ? $this->formatDateTime($this->before) : null,
			'limit'          => $this->limit,
			'starting_after' => $this->startingAfter,
			'ending_before'  => $this->endingBefore,
		], static fn ($value): bool => $value !== null);
	}
}
