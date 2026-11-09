<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Resources\Concerns;

use TEC\Common\LiquidWeb\LicensingApiClient\Http\AuthState;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Credit\CreditsLedgerResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Credit\CreditsPoolsResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Credit\CreditsQuotasResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Credit\CreditsResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\EntitlementsResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\LicensesResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\ProductsResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\TokensResource;

/**
 * Provides immutable auth-state rebinding for auth-bound resource views.
 *
 * @mixin CreditsLedgerResource
 * @mixin CreditsPoolsResource
 * @mixin CreditsQuotasResource
 * @mixin CreditsResource
 * @mixin EntitlementsResource
 * @mixin LicensesResource
 * @mixin ProductsResource
 * @mixin TokensResource
 */
trait RebindsAuthState
{
	/**
	 * Returns the current resource when the auth state is unchanged, or a rebound
	 * resource view when a different auth state is requested.
	 */
	public function withAuthState(AuthState $authState): self {
		if ($this->authState === $authState) {
			return $this;
		}

		return $this->rebindWithAuthState($authState);
	}

	/**
	 * Rebuilds the concrete resource with the provided auth state.
	 */
	abstract protected function rebindWithAuthState(AuthState $authState): self;
}
