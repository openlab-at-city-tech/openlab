<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Value;

use InvalidArgumentException;

/**
 * Represents a normalized non-empty authentication token value.
 */
final class AuthToken
{
	private string $token;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(string $token) {
		$token = trim($token);

		if ($token === '') {
			throw new InvalidArgumentException('Authentication token cannot be empty.');
		}

		$this->token = $token;
	}

	public function equals(self $authToken): bool {
		return $this->token === $authToken->get();
	}

	public function get(): string {
		return $this->token;
	}
}
