<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Listing;

/**
 * Defines the query parameters for listing licenses.
 *
 * Filters:
 * - `search` performs a broad text search across listable license fields.
 * - `identityId` narrows results to a specific external customer identity.
 * - `status` applies the server-side license status filter.
 * - `productSlug` and `tier` limit results to matching products.
 * - `aliasKey` narrows results to licenses with a matching alias.
 * - `domain` narrows results to licenses with an exact active activation on that domain.
 *
 * Cursor pagination:
 * - `startingAfter` requests the next page after a known item ID.
 * - `endingBefore` requests the previous page before a known item ID.
 * - `limit` controls the page size and defaults to the API default of 10.
 */
final class ListRequest
{
	public ?string $search;

	public ?string $identityId;

	public ?string $status;

	public ?string $productSlug;

	public ?string $tier;

	public ?int $startingAfter;

	public ?int $endingBefore;

	public int $limit;

	public ?string $aliasKey;

	public ?string $domain;

	public function __construct(
		?string $search = null,
		?string $identityId = null,
		?string $status = null,
		?string $productSlug = null,
		?string $tier = null,
		?int $startingAfter = null,
		?int $endingBefore = null,
		int $limit = 10,
		?string $aliasKey = null,
		?string $domain = null
	) {
		$this->search        = $search;
		$this->identityId    = $identityId;
		$this->status        = $status;
		$this->productSlug   = $productSlug;
		$this->tier          = $tier;
		$this->startingAfter = $startingAfter;
		$this->endingBefore  = $endingBefore;
		$this->limit         = $limit;
		$this->aliasKey      = $aliasKey;
		$this->domain        = $domain;
	}

	/**
	 * @return array{
	 *     search?: string,
	 *     identity_id?: string,
	 *     status?: string,
	 *     product_slug?: string,
	 *     tier?: string,
	 *     alias_key?: string,
	 *     domain?: string,
	 *     starting_after?: int,
	 *     ending_before?: int,
	 *     limit: int
	 * }
	 */
	public function toQuery(): array {
		return array_filter([
			'search'         => $this->search,
			'identity_id'    => $this->identityId,
			'status'         => $this->status,
			'product_slug'   => $this->productSlug,
			'tier'           => $this->tier,
			'alias_key'      => $this->aliasKey,
			'domain'         => $this->domain,
			'starting_after' => $this->startingAfter,
			'ending_before'  => $this->endingBefore,
			'limit'          => $this->limit,
		], static fn ($value): bool => $value !== null);
	}
}
