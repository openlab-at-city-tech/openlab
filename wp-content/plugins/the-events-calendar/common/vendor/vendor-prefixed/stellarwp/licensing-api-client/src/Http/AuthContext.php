<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Http;

use InvalidArgumentException;

/**
 * Represents the authentication mode the API client should use for a request.
 */
final class AuthContext
{
	public const MODE_AUTO       = 'auto';
	public const MODE_NONE       = 'none';
	public const MODE_CONFIGURED = 'configured';
	public const MODE_EXPLICIT   = 'explicit';

	private string $mode;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(string $mode = self::MODE_AUTO) {
		$this->assertValidMode($mode);

		$this->mode = $mode;
	}

	/**
	 * @throws InvalidArgumentException
	 */
	private function assertValidMode(string $mode): void {
		$validModes = [
			self::MODE_AUTO,
			self::MODE_NONE,
			self::MODE_CONFIGURED,
			self::MODE_EXPLICIT,
		];

		if ( ! in_array($mode, $validModes, true)) {
			throw new InvalidArgumentException('Unsupported auth mode [' . $mode . '].');
		}
	}

	public function requiresToken(): bool {
		return $this->mode === self::MODE_CONFIGURED || $this->mode === self::MODE_EXPLICIT;
	}

	public function equals(self $authContext): bool {
		return $this->mode === $authContext->mode();
	}

	public function mode(): string {
		return $this->mode;
	}
}
