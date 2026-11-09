<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Alias;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents an alias removal response.
 *
 * @implements Response<array{removed: int}>
 */
final class RemoveAliases implements Response
{
	public int $removed;

	private function __construct(int $removed) {
		$this->removed = $removed;
	}

	/**
	 * @param array{removed: int} $attributes
	 */
	public static function from(array $attributes): self {
		return new self($attributes['removed']);
	}

	/**
	 * @return array{removed: int}
	 */
	public function toArray(): array {
		return [
			'removed' => $this->removed,
		];
	}
}
