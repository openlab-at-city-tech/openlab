<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Resources\Concerns;

use TEC\Common\LiquidWeb\LicensingApiClient\Http\RequestHeaderCollection;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Credit\CreditsLedgerResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Credit\CreditsPoolsResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Credit\CreditsQuotasResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Credit\CreditsResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\EntitlementsResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\LicensesResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\ProductsResource;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\TokensResource;

/**
 * Provides immutable request-header rebinding for resource views.
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
trait RebindsRequestHeaderCollection
{
	public function withRequestHeaderCollection(RequestHeaderCollection $requestHeaderCollection): self {
		if ($this->requestHeaderCollection === $requestHeaderCollection) {
			return $this;
		}

		return $this->rebindWithRequestHeaderCollection($requestHeaderCollection);
	}

	abstract protected function rebindWithRequestHeaderCollection(RequestHeaderCollection $requestHeaderCollection): self;
}
