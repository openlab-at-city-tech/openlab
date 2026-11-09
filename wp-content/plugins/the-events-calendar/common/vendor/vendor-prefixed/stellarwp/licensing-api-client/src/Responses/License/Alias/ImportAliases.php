<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Alias;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Alias\ValueObjects\ImportedAlias;

/**
 * Represents an alias import response.
 *
 * @implements Response<array{
 *     imported: list<array{alias_key: string, product_slug?: string|null}>
 * }>
 */
final class ImportAliases implements Response
{
	/**
	 * @var ImportedAlias[]
	 */
	public array $imported;

	/**
	 * @param ImportedAlias[] $imported
	 */
	private function __construct(array $imported) {
		$this->imported = $imported;
	}

	/**
	 * @param array{
	 *     imported: list<array{alias_key: string, product_slug?: string|null}>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			array_map(
				static fn (array $alias): ImportedAlias => ImportedAlias::from($alias),
				$attributes['imported']
			)
		);
	}

	/**
	 * @return array{
	 *     imported: list<array{alias_key: string, product_slug: string|null}>
	 * }
	 */
	public function toArray(): array {
		return [
			'imported' => array_map(
				static fn (ImportedAlias $alias): array => $alias->toArray(),
				$this->imported
			),
		];
	}
}
