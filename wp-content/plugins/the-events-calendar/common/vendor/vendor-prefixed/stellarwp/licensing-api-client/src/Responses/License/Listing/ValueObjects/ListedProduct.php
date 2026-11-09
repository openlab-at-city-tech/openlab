<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Listing\ValueObjects;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Product\ValueObjects\Activations;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\ValueObjects\CapabilityCollection;

/**
 * Represents one product entry nested under a license listing response.
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
 *     }
 * }>
 */
final class ListedProduct implements Response
{
	use InteractsWithDateTime;

	public string $productSlug;

	public string $tier;

	public string $status;

	public DateTimeImmutable $expires;

	public CapabilityCollection $capabilities;

	public Activations $activations;

	private function __construct(
		string $productSlug,
		string $tier,
		string $status,
		DateTimeImmutable $expires,
		CapabilityCollection $capabilities,
		Activations $activations
	) {
		$this->productSlug  = $productSlug;
		$this->tier         = $tier;
		$this->status       = $status;
		$this->expires      = $expires;
		$this->capabilities = $capabilities;
		$this->activations  = $activations;
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
	 *     }
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
			Activations::from($attributes['activations'])
		);
	}

	public function toArray(): array {
		return [
			'product_slug' => $this->productSlug,
			'tier'         => $this->tier,
			'status'       => $this->status,
			'expires'      => $this->formatDateTime($this->expires),
			'capabilities' => $this->capabilities->toArray(),
			'activations'  => $this->activations->toArray(),
		];
	}

	public function isActive(): bool {
		return $this->status === 'active';
	}

	public function hasCapability(string $capability): bool {
		return $this->capabilities->has($capability);
	}
}
