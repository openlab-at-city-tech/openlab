<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\ValueObjects;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents the validation outcome for one requested product.
 *
 * @implements Response<array{
 *     product_slug: string,
 *     status: string,
 *     is_valid: bool,
 *     entitlement: array{
 *         tier: string,
 *         site_limit: int,
 *         active_count: int,
 *         available: int,
 *         over_limit: bool,
 *         excess_activations: int,
 *         expiration_date: string,
 *         status: string,
 *         capabilities: list<string>
 *     }|null,
 *     activation: array{
 *         domain: string,
 *         activated_at: string
 *     }|null,
 *     available_entitlements: list<array{
 *         tier: string,
 *         site_limit: int,
 *         active_count: int,
 *         available: int,
 *         over_limit: bool,
 *         excess_activations: int,
 *         capabilities: list<string>,
 *         status: string,
 *         expires: string
 *     }>
 * }>
 */
final class ProductValidation implements Response
{
	public string $productSlug;

	public string $status;

	public bool $isValid;

	public ?Entitlement $entitlement;

	public ?Activation $activation;

	/** @var list<AvailableEntitlement> */
	public array $availableEntitlements;

	/**
	 * @param list<AvailableEntitlement> $availableEntitlements
	 */
	private function __construct(
		string $productSlug,
		string $status,
		bool $isValid,
		?Entitlement $entitlement = null,
		?Activation $activation = null,
		array $availableEntitlements = []
	) {
		$this->productSlug           = $productSlug;
		$this->status                = $status;
		$this->isValid               = $isValid;
		$this->entitlement           = $entitlement;
		$this->activation            = $activation;
		$this->availableEntitlements = $availableEntitlements;
	}

	/**
	 * @param array{
	 *     product_slug: string,
	 *     status: string,
	 *     is_valid: bool,
	 *     entitlement: array{
	 *         tier: string,
	 *         site_limit: int,
	 *         active_count: int,
	 *         available: int,
	 *         over_limit: bool,
	 *         excess_activations: int,
	 *         expiration_date: string,
	 *         status: string,
	 *         capabilities: list<string>
	 *     }|null,
	 *     activation: array{
	 *         domain: string,
	 *         activated_at: string
	 *     }|null,
	 *     available_entitlements?: list<array{
	 *         tier: string,
	 *         site_limit: int,
	 *         active_count: int,
	 *         available: int,
	 *         over_limit: bool,
	 *         excess_activations: int,
	 *         capabilities: list<string>,
	 *         status: string,
	 *         expires: string
	 *     }>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		$entitlement = $attributes['entitlement'] ?? null;
		$activation  = $attributes['activation'] ?? null;

		return new self(
			$attributes['product_slug'],
			$attributes['status'],
			$attributes['is_valid'],
			$entitlement ? Entitlement::from($entitlement) : null,
			$activation ? Activation::from($activation) : null,
			array_map(
				static fn (array $availableEntitlement): AvailableEntitlement => AvailableEntitlement::from($availableEntitlement),
				$attributes['available_entitlements'] ?? []
			)
		);
	}

	public function toArray(): array {
		return [
			'product_slug'           => $this->productSlug,
			'status'                 => $this->status,
			'is_valid'               => $this->isValid,
			'entitlement'            => $this->entitlement ? $this->entitlement->toArray() : null,
			'activation'             => $this->activation ? $this->activation->toArray() : null,
			'available_entitlements' => array_map(
				static fn (AvailableEntitlement $availableEntitlement): array => $availableEntitlement->toArray(),
				$this->availableEntitlements
			),
		];
	}

	public function hasCapability(string $capability): bool {
		return $this->entitlement !== null
			&& $this->entitlement->capabilities->has($capability);
	}

	public function isCapabilityValid(string $capability): bool {
		return $this->isValid
			&& $this->hasCapability($capability);
	}
}
