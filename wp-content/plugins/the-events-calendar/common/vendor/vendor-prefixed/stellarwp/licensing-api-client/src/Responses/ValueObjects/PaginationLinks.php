<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\ValueObjects;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * @implements Response<array{
 *     first: string,
 *     last: string|null,
 *     prev: string|null,
 *     next: string|null
 * }>
 */
final class PaginationLinks implements Response
{
	public string $first;

	public ?string $last;

	public ?string $prev;

	public ?string $next;

	private function __construct(string $first, ?string $last, ?string $prev, ?string $next) {
		$this->first = $first;
		$this->last  = $last;
		$this->prev  = $prev;
		$this->next  = $next;
	}

	/**
	 * @param array{
	 *     first: string,
	 *     last: string|null,
	 *     prev: string|null,
	 *     next: string|null
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['first'],
			$attributes['last'],
			$attributes['prev'],
			$attributes['next']
		);
	}

	/**
	 * @return array{
	 *     first: string,
	 *     last: string|null,
	 *     prev: string|null,
	 *     next: string|null
	 * }
	 */
	public function toArray(): array {
		return [
			'first' => $this->first,
			'last'  => $this->last,
			'prev'  => $this->prev,
			'next'  => $this->next,
		];
	}
}
