<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\ValueObjects;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use LogicException;
use Traversable;

/**
 * Immutable ordered set of capability names.
 *
 * @implements Response<list<string>>
 * @implements ArrayAccess<int, string>
 * @implements IteratorAggregate<int, string>
 */
final class CapabilityCollection implements ArrayAccess, Countable, IteratorAggregate, Response
{
	/**
	 * @var list<string>
	 */
	private array $items;

	/**
	 * @var array<string, true>
	 */
	private array $lookup;

	/**
	 * @param list<string> $items
	 */
	private function __construct(array $items) {
		$this->items  = array_values(array_unique($items));
		$this->lookup = array_fill_keys($this->items, true);
	}

	/**
	 * @param array<int, string> $attributes
	 */
	public static function from(array $attributes): self {
		return new self(array_values($attributes));
	}

	public function has(string $capability): bool {
		return isset($this->lookup[$capability]);
	}

	public function isEmpty(): bool {
		return $this->items === [];
	}

	public function count(): int {
		return count($this->items);
	}

	/**
	 * @return ArrayIterator<int, string>
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator($this->items);
	}

	/**
	 * @param string|mixed $offset
	 */
	public function offsetExists($offset): bool {
		return is_int($offset) && isset($this->items[$offset]);
	}

	/**
	 * @param string|mixed $offset
	 */
	public function offsetGet($offset): ?string {
		if (! is_int($offset)) {
			return null;
		}

		return $this->items[$offset] ?? null;
	}

	public function toArray(): array {
		return $this->items;
	}

	/**
	 * @param string|mixed $offset
	 * @param mixed $value The value to set.
	 *
	 * @throws LogicException
	 */
	public function offsetSet($offset, $value): void {
		throw new LogicException('CapabilityCollection is immutable.');
	}

	/**
	 * @param string|mixed $offset
	 *
	 * @throws LogicException
	 */
	public function offsetUnset($offset): void {
		throw new LogicException('CapabilityCollection is immutable.');
	}
}
