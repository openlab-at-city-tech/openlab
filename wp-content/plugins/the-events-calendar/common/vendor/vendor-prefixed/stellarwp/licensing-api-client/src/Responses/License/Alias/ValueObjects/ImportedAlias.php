<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Alias\ValueObjects;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents one imported license alias.
 *
 * @implements Response<array{alias_key: string, product_slug: string|null}>
 */
final class ImportedAlias implements Response
{
	public string $aliasKey;

	public ?string $productSlug;

	private function __construct(string $aliasKey, ?string $productSlug) {
		$this->aliasKey    = $aliasKey;
		$this->productSlug = $productSlug;
	}

	/**
	 * @param array{alias_key: string, product_slug?: string|null} $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['alias_key'],
			$attributes['product_slug'] ?? null
		);
	}

	/**
	 * @return array{alias_key: string, product_slug: string|null}
	 */
	public function toArray(): array {
		return [
			'alias_key'    => $this->aliasKey,
			'product_slug' => $this->productSlug,
		];
	}
}
