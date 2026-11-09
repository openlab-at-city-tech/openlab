<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Token\ValueObjects;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * A single token in a token list or revoke response.
 *
 * @phpstan-type TokenItemPayload array{
 *     id: int,
 *     token: string,
 *     license_id: int,
 *     domain: string,
 *     is_revoked: bool,
 *     created_at: string,
 *     updated_at: string
 * }
 *
 * @implements Response<TokenItemPayload>
 */
final class TokenItem implements Response
{
	use InteractsWithDateTime;

	public int $id;

	public string $token;

	public int $licenseId;

	public string $domain;

	public bool $isRevoked;

	public DateTimeImmutable $createdAt;

	public DateTimeImmutable $updatedAt;

	private function __construct(
		int $id,
		string $token,
		int $licenseId,
		string $domain,
		bool $isRevoked,
		DateTimeImmutable $createdAt,
		DateTimeImmutable $updatedAt
	) {
		$this->id        = $id;
		$this->token     = $token;
		$this->licenseId = $licenseId;
		$this->domain    = $domain;
		$this->isRevoked = $isRevoked;
		$this->createdAt = $createdAt;
		$this->updatedAt = $updatedAt;
	}

	/**
	 * @param TokenItemPayload $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['id'],
			$attributes['token'],
			$attributes['license_id'],
			$attributes['domain'],
			$attributes['is_revoked'],
			self::parseDateTime($attributes['created_at']),
			self::parseDateTime($attributes['updated_at'])
		);
	}

	/**
	 * @return TokenItemPayload
	 */
	public function toArray(): array {
		return [
			'id'         => $this->id,
			'token'      => $this->token,
			'license_id' => $this->licenseId,
			'domain'     => $this->domain,
			'is_revoked' => $this->isRevoked,
			'created_at' => $this->formatDateTime($this->createdAt),
			'updated_at' => $this->formatDateTime($this->updatedAt),
		];
	}
}
