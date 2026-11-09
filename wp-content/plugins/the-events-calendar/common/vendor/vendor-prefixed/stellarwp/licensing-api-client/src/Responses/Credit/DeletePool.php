<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Credit;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents a credit pool deletion response.
 *
 * @implements Response<array{deleted: bool, pool_id: int}>
 */
final class DeletePool implements Response
{
	public bool $deleted;

	public int $poolId;

	private function __construct(bool $deleted, int $poolId) {
		$this->deleted = $deleted;
		$this->poolId  = $poolId;
	}

	/**
	 * @param array{deleted: bool, pool_id: int} $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['deleted'],
			$attributes['pool_id']
		);
	}

	/**
	 * @return array{deleted: bool, pool_id: int}
	 */
	public function toArray(): array {
		return [
			'deleted' => $this->deleted,
			'pool_id' => $this->poolId,
		];
	}
}
