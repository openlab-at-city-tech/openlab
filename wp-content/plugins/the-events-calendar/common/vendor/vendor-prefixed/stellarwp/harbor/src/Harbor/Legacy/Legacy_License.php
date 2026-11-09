<?php

declare(strict_types=1);

namespace TEC\Common\LiquidWeb\Harbor\Legacy;

use TEC\Common\LiquidWeb\Harbor\Utils\Cast;

/**
 * Represents a license key discovered from a plugin's legacy storage.
 *
 * @since 1.0.0
 */
class Legacy_License {

	/**
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public string $key;

	/**
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public string $slug;

	/**
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public string $name;

	/**
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public string $product;

	/**
	 * Whether the license is currently active.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public bool $is_active;

	/**
	 * Whether the reporting plugin opts this entry into Harbor's update pipeline.
	 *
	 * When true, the key is treated as compatible with Herald's `/legacy/download`
	 * endpoint (Stellar Licensing v3): it grants catalog feature availability for
	 * its slug and is used to build the download URL. When false (the default),
	 * the entry is informational only and continues to appear in the licensing UI
	 * and admin notices, but has no effect on feature availability or updates.
	 *
	 * Default false to avoid surfacing "update available" badges for keys whose
	 * legacy licensing backend is not Stellar/Herald-compatible, which would
	 * otherwise fail the actual server-side download.
	 *
	 * @since 1.3.0
	 *
	 * @var bool
	 */
	public bool $use_for_updates;

	/**
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public string $page_url;

	/**
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public string $expires_at;

	/**
	 * @since 1.0.0
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		return [
			'key'             => $this->key,
			'slug'            => $this->slug,
			'name'            => $this->name,
			'product'         => $this->product,
			'is_active'       => $this->is_active,
			'use_for_updates' => $this->use_for_updates,
			'page_url'        => $this->page_url,
			'expires_at'      => $this->expires_at,
		];
	}

	/**
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $data The legacy license data.
	 */
	public static function from_data( array $data ): Legacy_License {
		$self = new self();

		$self->key             = Cast::to_string( $data['key'] ?? '' );
		$self->slug            = Cast::to_string( $data['slug'] ?? '' );
		$self->name            = Cast::to_string( $data['name'] ?? '' );
		$self->product         = Cast::to_string( $data['product'] ?? '' );
		$self->is_active       = (bool) ( $data['is_active'] ?? false );
		$self->use_for_updates = (bool) ( $data['use_for_updates'] ?? false );
		$self->page_url        = Cast::to_string( $data['page_url'] ?? '' );
		$self->expires_at      = Cast::to_string( $data['expires_at'] ?? '' );

		return $self;
	}
}
