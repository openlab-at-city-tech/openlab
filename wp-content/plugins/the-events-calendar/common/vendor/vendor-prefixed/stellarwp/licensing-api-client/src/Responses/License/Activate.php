<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\ValueObjects\Activation;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\ValueObjects\ActivationEntitlement;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\ValueObjects\LicenseSummary;

/**
 * Represents a license activation response.
 *
 * @implements Response<array{
 *     status: string,
 *     is_valid: bool,
 *     is_production: bool,
 *     license: array{license_key: string, status: string}|null,
 *     entitlement: array{
 *         product_slug: string,
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
 *     }|null
 * }>
 */
final class Activate implements Response
{
	public string $status;

	public bool $isValid;

	public bool $isProduction;

	public ?LicenseSummary $license;

	public ?ActivationEntitlement $entitlement;

	public ?Activation $activation;

	private function __construct(
		string $status,
		bool $isValid,
		bool $isProduction,
		?LicenseSummary $license,
		?ActivationEntitlement $entitlement,
		?Activation $activation
	) {
		$this->status       = $status;
		$this->isValid      = $isValid;
		$this->isProduction = $isProduction;
		$this->license      = $license;
		$this->entitlement  = $entitlement;
		$this->activation   = $activation;
	}

	/**
	 * @param array{
	 *     status: string,
	 *     is_valid: bool,
	 *     is_production?: bool,
	 *     license: array{license_key: string, status: string}|null,
	 *     entitlement: array{
	 *         product_slug: string,
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
	 *     }|null
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		$license     = $attributes['license'] ?? null;
		$entitlement = $attributes['entitlement'] ?? null;
		$activation  = $attributes['activation'] ?? null;

		return new self(
			$attributes['status'],
			$attributes['is_valid'],
			$attributes['is_production'] ?? true,
			$license ? LicenseSummary::from($license) : null,
			$entitlement ? ActivationEntitlement::from($entitlement) : null,
			$activation ? Activation::from($activation) : null
		);
	}

	public function toArray(): array {
		return [
			'status'        => $this->status,
			'is_valid'      => $this->isValid,
			'is_production' => $this->isProduction,
			'license'       => $this->license ? $this->license->toArray() : null,
			'entitlement'   => $this->entitlement ? $this->entitlement->toArray() : null,
			'activation'    => $this->activation ? $this->activation->toArray() : null,
		];
	}
}
