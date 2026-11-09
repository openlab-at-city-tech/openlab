<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient;

use TEC\Common\LiquidWeb\LicensingApiClient\Contracts\LicensingClientInterface;
use TEC\Common\LiquidWeb\LicensingApiClient\Http\AuthState;
use TEC\Common\LiquidWeb\LicensingApiClient\Http\RequestHeaderCollection;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Contracts\CreditsResourceInterface;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Contracts\EntitlementsResourceInterface;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Contracts\LicensesResourceInterface;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Contracts\ProductsResourceInterface;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Contracts\TokensResourceInterface;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Credit\CreditsResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\EntitlementsResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\LicensesResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\ProductsResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\TokensResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Tracing\TraceContext;
use TEC\Common\LiquidWeb\LicensingApiClient\Tracing\TraceParent;

/**
 * Exposes the built API resources and immutable auth-state transitions.
 */
final class Api implements LicensingClientInterface
{
	private AuthState $authState;

	private RequestHeaderCollection $requestHeaderCollection;

	private LicensesResource $licenses;

	private ProductsResource $products;

	private CreditsResource $credits;

	private EntitlementsResource $entitlements;

	private TokensResource $tokens;

	public function __construct(
		AuthState $authState,
		RequestHeaderCollection $requestHeaderCollection,
		LicensesResource $licenses,
		ProductsResource $products,
		CreditsResource $credits,
		EntitlementsResource $entitlements,
		TokensResource $tokens
	) {
		$this->authState               = $authState;
		$this->requestHeaderCollection = $requestHeaderCollection;
		$this->licenses                = $licenses;
		$this->products                = $products;
		$this->credits                 = $credits;
		$this->entitlements            = $entitlements;
		$this->tokens                  = $tokens;
	}

	public function entitlements(): EntitlementsResourceInterface {
		return $this->entitlements;
	}

	public function licenses(): LicensesResourceInterface {
		return $this->licenses;
	}

	public function products(): ProductsResourceInterface {
		return $this->products;
	}

	public function credits(): CreditsResourceInterface {
		return $this->credits;
	}

	public function tokens(): TokensResourceInterface {
		return $this->tokens;
	}

	public function withoutAuth(): LicensingClientInterface {
		return $this->cloneWithAuthState($this->authState->withoutAuth());
	}

	private function cloneWithAuthState(AuthState $authState): self {
		return new self(
			$authState,
			$this->requestHeaderCollection,
			$this->licenses->withAuthState($authState),
			$this->products->withAuthState($authState),
			$this->credits->withAuthState($authState),
			$this->entitlements->withAuthState($authState),
			$this->tokens->withAuthState($authState)
		);
	}

	/**
	 * @param array<string, string|int|float|bool> $headers
	 */
	public function withHeaders(array $headers): LicensingClientInterface {
		return $this->cloneWithRequestHeaderCollection($this->requestHeaderCollection->withHeaders($headers));
	}

	public function withTraceParent(TraceParent $traceParent): LicensingClientInterface {
		return $this->cloneWithRequestHeaderCollection($this->requestHeaderCollection->withTraceParent($traceParent));
	}

	public function withTraceContext(TraceContext $traceContext): LicensingClientInterface {
		return $this->cloneWithRequestHeaderCollection($this->requestHeaderCollection->withTraceContext($traceContext));
	}

	public function withoutHeaders(): LicensingClientInterface {
		return $this->cloneWithRequestHeaderCollection($this->requestHeaderCollection->withoutHeaders());
	}

	private function cloneWithRequestHeaderCollection(RequestHeaderCollection $requestHeaderCollection): self {
		if ($this->requestHeaderCollection === $requestHeaderCollection) {
			return $this;
		}

		return new self(
			$this->authState,
			$requestHeaderCollection,
			$this->licenses->withRequestHeaderCollection($requestHeaderCollection),
			$this->products->withRequestHeaderCollection($requestHeaderCollection),
			$this->credits->withRequestHeaderCollection($requestHeaderCollection),
			$this->entitlements->withRequestHeaderCollection($requestHeaderCollection),
			$this->tokens->withRequestHeaderCollection($requestHeaderCollection)
		);
	}

	public function withConfiguredToken(): LicensingClientInterface {
		return $this->cloneWithAuthState($this->authState->withConfiguredToken());
	}

	public function withToken(string $token): LicensingClientInterface {
		return $this->cloneWithAuthState($this->authState->withToken($token));
	}
}
