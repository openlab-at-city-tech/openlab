<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Token;

use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Token\ValueObjects\TokenItem;

/**
 * Represents a token list response.
 *
 * @implements Response<array{
 *     tokens: list<array{
 *         id: int,
 *         token: string,
 *         license_id: int,
 *         domain: string,
 *         is_revoked: bool,
 *         created_at: string,
 *         updated_at: string
 *     }>
 * }>
 */
final class TokenList implements Response
{
	/** @var list<TokenItem> */
	public array $tokens;

	/**
	 * @param list<TokenItem> $tokens
	 */
	private function __construct(array $tokens) {
		$this->tokens = $tokens;
	}

	/**
	 * @param array{
	 *     tokens: list<array{
	 *         id: int,
	 *         token: string,
	 *         license_id: int,
	 *         domain: string,
	 *         is_revoked: bool,
	 *         created_at: string,
	 *         updated_at: string
	 *     }>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			array_map(
				static fn(array $item): TokenItem => TokenItem::from($item),
				$attributes['tokens']
			)
		);
	}

	public function toArray(): array {
		return [
			'tokens' => array_map(
				static fn(TokenItem $item): array => $item->toArray(),
				$this->tokens
			),
		];
	}
}
