<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Product\ValueObjects;

use DateTimeImmutable;
use DateTimeZone;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\ValueObjects\CapabilityCollection;

/**
 * Represents one entitlement entry in the product catalog.
 *
 * @phpstan-type ActivationDomainPayload array{
 *     activated_at: string,
 *     deactivated_at: string|null,
 *     is_active: bool,
 *     is_production: bool
 * }
 * @phpstan-type ActivationDomainsPayload array<string, ActivationDomainPayload>
 *
 * @implements Response<array{
 *     product_slug: string,
 *     tier: string,
 *     status: string,
 *     expires: string,
 *     capabilities: list<string>,
 *     activations: array{
 *         site_limit: int,
 *         active_count: int,
 *         over_limit: bool,
 *         excess_activations: int,
 *         domains: ActivationDomainsPayload
 *     },
 *     activated_here?: bool,
 *     validation_status?: string,
 *     is_valid?: bool
 * }>
 */
final class CatalogEntry implements Response
{
	use InteractsWithDateTime;

	public string $productSlug;

	public string $tier;

	public string $status;

	public DateTimeImmutable $expires;

	public CapabilityCollection $capabilities;

	public Activations $activations;

	public ?bool $activatedHere;

	public ?string $validationStatus;

	public ?bool $isValid;

	private function __construct(
		string $productSlug,
		string $tier,
		string $status,
		DateTimeImmutable $expires,
		CapabilityCollection $capabilities,
		Activations $activations,
		?bool $activatedHere = null,
		?string $validationStatus = null,
		?bool $isValid = null
	) {
		$this->productSlug      = $productSlug;
		$this->tier             = $tier;
		$this->status           = $status;
		$this->expires          = $expires;
		$this->capabilities     = $capabilities;
		$this->activations      = $activations;
		$this->activatedHere    = $activatedHere;
		$this->validationStatus = $validationStatus;
		$this->isValid          = $isValid;
	}

	/**
	 * @param array{
	 *     product_slug: string,
	 *     tier: string,
	 *     status: string,
	 *     expires: string,
	 *     capabilities: list<string>,
	 *     activations: array{
	 *         site_limit: int,
	 *         active_count: int,
	 *         over_limit: bool,
	 *         excess_activations: int,
	 *         domains: ActivationDomainsPayload
	 *     },
	 *     activated_here?: bool,
	 *     validation_status?: string,
	 *     is_valid?: bool
	 * } $attributes
	 *
	 * @throws UnexpectedResponseException
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['product_slug'],
			$attributes['tier'],
			$attributes['status'],
			self::parseDateTime($attributes['expires']),
			CapabilityCollection::from($attributes['capabilities']),
			Activations::from($attributes['activations']),
			$attributes['activated_here'] ?? null,
			$attributes['validation_status'] ?? null,
			$attributes['is_valid'] ?? null
		);
	}

	public function toArray(): array {
		return array_merge([
			'product_slug' => $this->productSlug,
			'tier'         => $this->tier,
			'status'       => $this->status,
			'expires'      => $this->formatDateTime($this->expires),
			'capabilities' => $this->capabilities->toArray(),
			'activations'  => $this->activations->toArray(),
		], array_filter([
			'activated_here'    => $this->activatedHere,
			'validation_status' => $this->validationStatus,
			'is_valid'          => $this->isValid,
		], static fn ($value): bool => $value !== null));
	}

	public function hasActiveStatus(): bool {
		return $this->status === 'active';
	}

	public function isExpired(): bool {
		return $this->expires < new DateTimeImmutable('now', new DateTimeZone('UTC'));
	}

	public function isActive(): bool {
		return $this->hasActiveStatus()
			&& ! $this->isExpired();
	}

	public function hasCurrentSiteValidation(): bool {
		return $this->validationStatus !== null;
	}

	public function isValidForCurrentSite(): bool {
		return $this->hasCurrentSiteValidation()
			&& $this->isValid       === true
			&& $this->activatedHere === true;
	}

	public function hasCapability(string $capability): bool {
		return $this->capabilities->has($capability);
	}

	public function isCapabilityValid(string $capability): bool {
		return $this->isActive()
			&& $this->isValidForCurrentSite()
			&& $this->hasCapability($capability);
	}
}
