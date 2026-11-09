<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Tracing;

use InvalidArgumentException;

/**
 * Immutable W3C traceparent value object.
 *
 * @see https://www.w3.org/TR/trace-context/#traceparent-header
 */
final class TraceParent
{
	private string $version;

	private string $traceId;

	private string $parentSpanId;

	private string $flags;

	private function __construct(string $version, string $traceId, string $parentSpanId, string $flags) {
		$this->version      = $version;
		$this->traceId      = $traceId;
		$this->parentSpanId = $parentSpanId;
		$this->flags        = $flags;
	}

	/**
	 * Parse and validate a W3C traceparent header value.
	 *
	 * @throws InvalidArgumentException
	 */
	public static function fromString(string $value): self {
		$value = trim($value);

		if ($value === '') {
			throw new InvalidArgumentException('traceparent cannot be empty.');
		}

		$parts = explode('-', strtolower($value));

		if (count($parts) !== 4) {
			throw new InvalidArgumentException('traceparent must have 4 dash-delimited parts.');
		}

		$version      = $parts[0];
		$traceId      = $parts[1];
		$parentSpanId = $parts[2];
		$flags        = $parts[3];

		if (!self::isHex($version, 2) || $version === 'ff') {
			throw new InvalidArgumentException('traceparent version must be 2 hex characters and not ff.');
		}

		if (!self::isIdentifier($traceId, 32)) {
			throw new InvalidArgumentException('traceparent trace-id must be 32 non-zero hex characters.');
		}

		if (!self::isIdentifier($parentSpanId, 16)) {
			throw new InvalidArgumentException('traceparent parent-id must be 16 non-zero hex characters.');
		}

		if (!self::isHex($flags, 2)) {
			throw new InvalidArgumentException('traceparent flags must be 2 hex characters.');
		}

		return new self($version, $traceId, $parentSpanId, $flags);
	}

	/**
	 * Generate a new root traceparent value.
	 */
	public static function generate(bool $sampled = true): self {
		return new self(
			'00',
			bin2hex(random_bytes(16)),
			bin2hex(random_bytes(8)),
			$sampled ? '01' : '00'
		);
	}

	/**
	 * Return the normalized traceparent header string.
	 */
	public function header(): string {
		return sprintf(
			'%s-%s-%s-%s',
			$this->version,
			$this->traceId,
			$this->parentSpanId,
			$this->flags
		);
	}

	/**
	 * Return the trace-id segment.
	 */
	public function traceId(): string {
		return $this->traceId;
	}

	/**
	 * Return the parent span-id segment.
	 */
	public function parentSpanId(): string {
		return $this->parentSpanId;
	}

	/**
	 * Return the trace-flags segment.
	 */
	public function flags(): string {
		return $this->flags;
	}

	public function __toString(): string {
		return $this->header();
	}

	private static function isHex(string $value, int $length): bool {
		return strlen($value) === $length && ctype_xdigit($value);
	}

	private static function isIdentifier(string $value, int $length): bool {
		return self::isHex($value, $length) && trim($value, '0') !== '';
	}
}
