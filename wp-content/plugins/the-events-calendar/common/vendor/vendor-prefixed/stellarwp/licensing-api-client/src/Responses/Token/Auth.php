<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Token;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents a token authorization check response.
 *
 * @implements Response<array{authorized: bool}>
 */
final class Auth implements Response
{
	public bool $authorized;

	private function __construct(bool $authorized) {
		$this->authorized = $authorized;
	}

	/**
	 * @param array{authorized: bool} $attributes
	 */
	public static function from(array $attributes): self {
		return new self($attributes['authorized']);
	}

	public function toArray(): array {
		return [
			'authorized' => $this->authorized,
		];
	}
}
