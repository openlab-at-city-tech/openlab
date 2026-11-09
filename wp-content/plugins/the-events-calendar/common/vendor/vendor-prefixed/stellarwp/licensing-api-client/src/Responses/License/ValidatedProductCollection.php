<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\ValueObjects\ProductValidation;
use LogicException;
use Traversable;

/**
 * Immutable collection of validated products with helper filters.
 *
 * @implements Response<list<array{
 *     product_slug: string,
 *     status: string,
 *     is_valid: bool,
 *     entitlement: array{
 *         tier: string,
 *         site_limit: int,
 *         active_count: int,
 *         available: int,
 *         over_limit: bool,
 *         excess_activations: int,
 *         expiration_date: string,
 *         status: string,
 *         capabilities: list<string>
 *     }|null,
 *     activation: array{
 *         domain: string,
 *         activated_at: string
 *     }|null,
 *     available_entitlements: list<array{
 *         tier: string,
 *         site_limit: int,
 *         active_count: int,
 *         available: int,
 *         over_limit: bool,
 *         excess_activations: int,
 *         capabilities: list<string>,
 *         status: string,
 *         expires: string
 *     }>
 * }>>
 * @implements ArrayAccess<int, ProductValidation>
 * @implements IteratorAggregate<int, ProductValidation>
 */
final class ValidatedProductCollection implements ArrayAccess, Countable, IteratorAggregate, Response
{
	/**
	 * @var list<ProductValidation>
	 */
	private array $items;

	/**
	 * @param list<ProductValidation> $items
	 */
	private function __construct(array $items) {
		$this->items = array_values($items);
	}

	/**
	 * @param list<array{
	 *     product_slug: string,
	 *     status: string,
	 *     is_valid: bool,
	 *     entitlement: array{
	 *         tier: string,
	 *         site_limit: int,
	 *         active_count: int,
	 *         available: int,
	 *         over_limit: bool,
	 *         excess_activations: int,
	 *         expiration_date: string,
	 *         status: string,
	 *         capabilities: list<string>
	 *     }|null,
	 *     activation: array{
	 *         domain: string,
	 *         activated_at: string
	 *     }|null,
	 *     available_entitlements?: list<array{
	 *         tier: string,
	 *         site_limit: int,
	 *         active_count: int,
	 *         available: int,
	 *         over_limit: bool,
	 *         excess_activations: int,
	 *         capabilities: list<string>,
	 *         status: string,
	 *         expires: string
	 *     }>
	 * }> $attributes
	 */
	public static function from(array $attributes): self {
		return new self(array_map(
			static fn (array $product): ProductValidation => ProductValidation::from($product),
			$attributes
		));
	}

	/**
	 * Return only validations for a specific product slug.
	 */
	public function forProduct(string $productSlug): self {
		return new self(array_values(array_filter(
			$this->items,
			static fn (ProductValidation $product): bool => $product->productSlug === $productSlug
		)));
	}

	/**
	 * Return only validations that are currently valid for the requested domain.
	 */
	public function valid(): self {
		return new self(array_values(array_filter(
			$this->items,
			static fn (ProductValidation $product): bool => $product->isValid
		)));
	}

	/**
	 * Return the first validation in the collection, or null when it is empty.
	 */
	public function first(): ?ProductValidation {
		return $this->items[0] ?? null;
	}

	/**
	 * Return the underlying product validations as a raw object array.
	 *
	 * @return list<ProductValidation>
	 */
	public function all(): array {
		return $this->items;
	}

	/**
	 * Determine whether any validation in the current collection exposes a capability.
	 */
	public function hasCapability(string $capability): bool {
		foreach ($this->items as $product) {
			if ($product->hasCapability($capability)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determine whether any validation in the current collection validly grants a capability.
	 *
	 * This is intended for already-filtered collections, such as the result of
	 * {@see self::forProduct()}, where the caller wants to know whether any remaining
	 * valid and active product validation grants the requested capability.
	 */
	public function hasValidCapability(string $capability): bool {
		foreach ($this->items as $product) {
			if ($product->isCapabilityValid($capability)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determine whether any validation for the given product slug validly grants a capability.
	 *
	 * This is the top-level convenience API for the common question:
	 * "Does this product currently validate for capability X on this site?"
	 */
	public function isCapabilityValid(string $productSlug, string $capability): bool {
		return $this->forProduct($productSlug)->hasValidCapability($capability);
	}

	public function count(): int {
		return count($this->items);
	}

	/**
	 * @return ArrayIterator<int, ProductValidation>
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
	public function offsetGet($offset): ?ProductValidation {
		if (! is_int($offset)) {
			return null;
		}

		return $this->items[$offset] ?? null;
	}

	public function toArray(): array {
		return array_map(
			static fn (ProductValidation $product): array => $product->toArray(),
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
		throw new LogicException('ValidatedProductCollection is immutable.');
	}

	/**
	 * @param int|string|mixed $offset
	 *
	 * @throws LogicException
	 */
	public function offsetUnset($offset): void {
		throw new LogicException('ValidatedProductCollection is immutable.');
	}
}
