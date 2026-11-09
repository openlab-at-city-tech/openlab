<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Alias;

use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Alias\ValueObjects\ImportAlias;

/**
 * Represents an alias import request payload.
 *
 * @phpstan-import-type ImportAliasPayload from ImportAlias
 * @phpstan-type ImportAliasesPayload array{
 *     identity_id: string,
 *     aliases: list<ImportAliasPayload>
 * }
 */
final class ImportAliases
{
	/**
	 * Identity identifier that owns the aliases.
	 *
	 * @example identity_123
	 */
	public string $identityId;

	/**
	 * @var ImportAlias[]
	 */
	public array $aliases;

	/**
	 * @param ImportAlias[] $aliases
	 */
	public function __construct(string $identityId, array $aliases) {
		$this->identityId = $identityId;
		$this->aliases    = $aliases;
	}

	/**
	 * @return ImportAliasesPayload
	 */
	public function toArray(): array {
		return [
			'identity_id' => $this->identityId,
			'aliases'     => array_map(
				static fn (ImportAlias $alias): array => $alias->toArray(),
				$this->aliases
			),
		];
	}
}
