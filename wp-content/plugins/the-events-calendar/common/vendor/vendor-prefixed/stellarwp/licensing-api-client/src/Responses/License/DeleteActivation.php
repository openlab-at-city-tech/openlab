<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents a license activation deletion response.
 *
 * @implements Response<array{deleted: bool}>
 */
final class DeleteActivation implements Response
{
	public bool $deleted;

	private function __construct(bool $deleted) {
		$this->deleted = $deleted;
	}

	/**
	 * @param array{deleted: bool} $attributes
	 */
	public static function from(array $attributes): self {
		return new self($attributes['deleted']);
	}

	/**
	 * @return array{deleted: bool}
	 */
	public function toArray(): array {
		return [
			'deleted' => $this->deleted,
		];
	}
}
