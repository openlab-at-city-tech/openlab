<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Tracing;

/**
 * Immutable trace header pair for outbound request propagation.
 */
final class TraceContext
{
	private TraceParent $traceParent;

	private ?string $traceState;

	private function __construct(TraceParent $traceParent, ?string $traceState) {
		$this->traceParent = $traceParent;
		$this->traceState  = $traceState;
	}

	/**
	 * Generate a new trace context with a fresh traceparent and no tracestate.
	 */
	public static function generate(bool $sampled = true): self {
		return new self(TraceParent::generate($sampled), null);
	}

	/**
	 * Create a trace context from a validated traceparent and optional tracestate.
	 */
	public static function fromValues(TraceParent $traceParent, ?string $traceState = null): self {
		return new self($traceParent, self::normalizeTraceState($traceState));
	}

	/**
	 * Return the traceparent value object.
	 */
	public function traceParent(): TraceParent {
		return $this->traceParent;
	}

	/**
	 * Return the normalized tracestate header when present.
	 */
	public function traceState(): ?string {
		return $this->traceState;
	}

	/**
	 * Return the headers that should be applied to an outbound request.
	 *
	 * @return array<string, string>
	 */
	public function headers(): array {
		$headers = [
			'traceparent' => $this->traceParent->header(),
		];

		if ($this->traceState !== null) {
			$headers['tracestate'] = $this->traceState;
		}

		return $headers;
	}

	private static function normalizeTraceState(?string $traceState): ?string {
		if ($traceState === null) {
			return null;
		}

		$traceState = trim($traceState);

		return $traceState === '' ? null : $traceState;
	}
}
