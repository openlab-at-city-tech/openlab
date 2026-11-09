<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Alias\ValueObjects;

/**
 * Represents one alias import item.
 *
 * @phpstan-type ImportAliasPayload array{
 *     key: string,
 *     product_slug?: string
 * }
 */
final class ImportAlias
{
	/**
	 * Legacy alias key to import.
	 *
	 * @example LEGACY-KEY-123
	 */
	public string $key;

	/**
	 * Optional product scope for the alias.
	 *
	 * @example plugin-pro
	 */
	public ?string $productSlug;

	public function __construct(string $key, ?string $productSlug = null) {
		$this->key         = $key;
		$this->productSlug = $productSlug;
	}

	/**
	 * @return ImportAliasPayload
	 */
	public function toArray(): array {
		return array_filter([
			'key'          => $this->key,
			'product_slug' => $this->productSlug,
		], static fn ($value): bool => $value !== null);
	}
}
