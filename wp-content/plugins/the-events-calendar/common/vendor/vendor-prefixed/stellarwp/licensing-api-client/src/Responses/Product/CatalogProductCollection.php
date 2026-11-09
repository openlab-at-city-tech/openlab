<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Product;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Product\ValueObjects\CatalogEntry;
use LogicException;
use Traversable;

/**
 * Immutable collection of catalog product entries with filtering helpers.
 *
 * @phpstan-type ActivationDomainPayload array{
 *     activated_at: string,
 *     deactivated_at: string|null,
 *     is_active: bool,
 *     is_production: bool
 * }
 * @phpstan-type ActivationDomainsPayload array<string, ActivationDomainPayload>
 *
 * @implements Response<list<array{
 *     product_slug: string,
 *     tier: string,
 *     status: string,
 *     expires: string,
 *     capabilities: list<string>,
 *     activations: array{
 *         site_limit: int,
 *         active_count: int,
 *         over_limit: bool,
 *         excess_activations: int,
 *         domains: ActivationDomainsPayload
 *     },
 *     activated_here?: bool,
 *     validation_status?: string,
 *     is_valid?: bool
 * }>>
 * @implements ArrayAccess<int, CatalogEntry>
 * @implements IteratorAggregate<int, CatalogEntry>
 */
final class CatalogProductCollection implements ArrayAccess, Countable, IteratorAggregate, Response
{
	/**
	 * @var list<CatalogEntry>
	 */
	private array $items;

	/**
	 * @param list<CatalogEntry> $items
	 */
	private function __construct(array $items) {
		$this->items = array_values($items);
	}

	/**
	 * @param list<array{
	 *     product_slug: string,
	 *     tier: string,
	 *     status: string,
	 *     expires: string,
	 *     capabilities: list<string>,
	 *     activations: array{
	 *         site_limit: int,
	 *         active_count: int,
	 *         over_limit: bool,
	 *         excess_activations: int,
	 *         domains: ActivationDomainsPayload
	 *     },
	 *     activated_here?: bool,
	 *     validation_status?: string,
	 *     is_valid?: bool
	 * }> $attributes
	 */
	public static function from(array $attributes): self {
		return new self(array_map(
			static fn (array $entry): CatalogEntry => CatalogEntry::from($entry),
			$attributes
		));
	}

	/**
	 * Return only entries for a specific product slug.
	 */
	public function forProduct(string $productSlug): self {
		return new self(array_values(array_filter(
			$this->items,
			static fn (CatalogEntry $entry): bool => $entry->productSlug === $productSlug
		)));
	}

	/**
	 * Return only entries whose entitlement is currently active.
	 */
	public function active(): self {
		return new self(array_values(array_filter(
			$this->items,
			static fn (CatalogEntry $entry): bool => $entry->isActive()
		)));
	}

	/**
	 * Return only entries that are valid for the current site context.
	 */
	public function valid(): self {
		return new self(array_values(array_filter(
			$this->items,
			static fn (CatalogEntry $entry): bool => $entry->isValidForCurrentSite()
		)));
	}

	/**
	 * Return the first entry in the collection, or null when it is empty.
	 */
	public function first(): ?CatalogEntry {
		return $this->items[0] ?? null;
	}

	/**
	 * Return the underlying catalog entries as a raw object array.
	 *
	 * @return list<CatalogEntry>
	 */
	public function all(): array {
		return $this->items;
	}

	/**
	 * Determine whether any entry in the current collection exposes a capability.
	 */
	public function hasCapability(string $capability): bool {
		foreach ($this->items as $entry) {
			if ($entry->hasCapability($capability)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determine whether any entry in the current collection validly grants a capability.
	 *
	 * This is intended for already-filtered collections, such as the result of
	 * {@see self::forProduct()}, where the caller wants to know whether any remaining
	 * active and valid entry grants the requested capability.
	 */
	public function hasValidCapability(string $capability): bool {
		foreach ($this->items as $entry) {
			if ($entry->isCapabilityValid($capability)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determine whether any entry for the given product slug validly grants a capability.
	 *
	 * This is the top-level convenience API for the common question:
	 * "Does this product currently grant capability X on this site?"
	 */
	public function isCapabilityValid(string $productSlug, string $capability): bool {
		return $this->forProduct($productSlug)->hasValidCapability($capability);
	}

	public function count(): int {
		return count($this->items);
	}

	/**
	 * @return ArrayIterator<int, CatalogEntry>
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator($this->items);
	}

	/**
	 * @param int|string|mixed $offset
	 */
	public function offsetExists($offset): bool {
		return is_int($offset) && isset($this->items[$offset]);
	}

	/**
	 * @param int|string|mixed $offset
	 */
	public function offsetGet($offset): ?CatalogEntry {
		if (! is_int($offset)) {
			return null;
		}

		return $this->items[$offset] ?? null;
	}

	public function toArray(): array {
		return array_map(
			static fn (CatalogEntry $entry): array => $entry->toArray(),
			$this->items
		);
	}

	/**
	 * @param int|string|mixed $offset
	 * @param mixed $value Ignored because the collection is immutable.
	 *
	 * @throws LogicException
	 */
	public function offsetSet($offset, $value): void {
		throw new LogicException('CatalogProductCollection is immutable.');
	}

	/**
	 * @param int|string|mixed $offset
	 *
	 * @throws LogicException
	 */
	public function offsetUnset($offset): void {
		throw new LogicException('CatalogProductCollection is immutable.');
	}
}
