<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents a license deactivation response.
 *
 * @implements Response<array{deactivated: bool}>
 */
final class Deactivate implements Response
{
	public bool $deactivated;

	private function __construct(bool $deactivated) {
		$this->deactivated = $deactivated;
	}

	/**
	 * @param array{deactivated: bool} $attributes
	 */
	public static function from(array $attributes): self {
		return new self($attributes['deactivated']);
	}

	/**
	 * @return array{deactivated: bool}
	 */
	public function toArray(): array {
		return [
			'deactivated' => $this->deactivated,
		];
	}
}
