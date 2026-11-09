<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Listing;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Listing\ValueObjects\LicenseListItem;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\ValueObjects\PageMeta;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\ValueObjects\PaginationLinks;

/**
 * Represents a cursor-paginated license listing.
 *
 * @phpstan-type ActivationDomainPayload array{
 *     activated_at: string,
 *     deactivated_at: string|null,
 *     is_active: bool,
 *     is_production: bool
 * }
 * @phpstan-type ActivationDomainsPayload array<string, ActivationDomainPayload>
 *
 * @implements Response<array{
 *     licenses: list<array{
 *         license_key: string,
 *         identity_id: string,
 *         status: string,
 *         created_at: string,
 *         updated_at: string,
 *         products: list<array{
 *             product_slug: string,
 *             tier: string,
 *             status: string,
 *             expires: string,
 *             capabilities: list<string>,
 *             activations: array{
 *                 site_limit: int,
 *                 active_count: int,
 *                 over_limit: bool,
 *                 excess_activations: int,
 *                 domains: ActivationDomainsPayload
 *             }
 *         }>,
 *         aliases: list<array{alias_key: string, product_slug: string|null}>
 *     }>,
 *     links: array{
 *         first: string,
 *         last: string|null,
 *         prev: string|null,
 *         next: string|null
 *     },
 *     meta: array{
 *         page: array{
 *             total: int,
 *             limit: int,
 *             max_size: int
 *         }
 *     }
 * }>
 */
final class Listing implements Response
{
	/**
	 * @var LicenseListItem[]
	 */
	public array $licenses;

	public PaginationLinks $links;

	public PageMeta $page;

	/**
	 * @param LicenseListItem[] $licenses
	 */
	private function __construct(array $licenses, PaginationLinks $links, PageMeta $page) {
		$this->licenses = $licenses;
		$this->links    = $links;
		$this->page     = $page;
	}

	/**
	 * @param array{
	 *     licenses: list<array{
	 *         license_key: string,
	 *         identity_id: string,
	 *         status: string,
	 *         created_at: string,
	 *         updated_at: string,
	 *         products: list<array{
	 *             product_slug: string,
	 *             tier: string,
	 *             status: string,
	 *             expires: string,
	 *             capabilities: list<string>,
	 *             activations: array{
	 *                 site_limit: int,
	 *                 active_count: int,
	 *                 over_limit: bool,
	 *                 excess_activations: int,
	 *                 domains: ActivationDomainsPayload
	 *             }
	 *         }>,
	 *         aliases: list<array{alias_key: string, product_slug?: string|null}>
	 *     }>,
	 *     links: array{
	 *         first: string,
	 *         last: string|null,
	 *         prev: string|null,
	 *         next: string|null
	 *     },
	 *     meta: array{
	 *         page: array{
	 *             total: int,
	 *             limit: int,
	 *             max_size: int
	 *         }
	 *     }
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			array_map(static fn (array $license): LicenseListItem => LicenseListItem::from($license), $attributes['licenses']),
			PaginationLinks::from($attributes['links']),
			PageMeta::from($attributes['meta']['page'])
		);
	}

	/**
	 * @return array{
	 *     licenses: list<array{
	 *         license_key: string,
	 *         identity_id: string,
	 *         status: string,
	 *         created_at: string,
	 *         updated_at: string,
	 *         products: list<array{
	 *             product_slug: string,
	 *             tier: string,
	 *             status: string,
	 *             expires: string,
	 *             capabilities: list<string>,
	 *             activations: array{
	 *                 site_limit: int,
	 *                 active_count: int,
	 *                 over_limit: bool,
	 *                 excess_activations: int,
	 *                 domains: ActivationDomainsPayload
	 *             }
	 *         }>,
	 *         aliases: list<array{alias_key: string, product_slug: string|null}>
	 *     }>,
	 *     links: array{
	 *         first: string,
	 *         last: string|null,
	 *         prev: string|null,
	 *         next: string|null
	 *     },
	 *     meta: array{
	 *         page: array{
	 *             total: int,
	 *             limit: int,
	 *             max_size: int
	 *         }
	 *     }
	 * }
	 */
	public function toArray(): array {
		return [
			'licenses' => array_map(
				static fn (LicenseListItem $license): array => $license->toArray(),
				$this->licenses
			),
			'links'    => $this->links->toArray(),
			'meta'     => [
				'page' => $this->page->toArray(),
			],
		];
	}
}
