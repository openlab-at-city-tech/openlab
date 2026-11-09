<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features\Contracts;

use TEC\Common\LiquidWeb\Harbor\Portal\Contracts\Download_Url_Builder;
use TEC\Common\LiquidWeb\Harbor\Portal\Results\Catalog_Feature;

/**
 * Contract for feature types that can be installed as WordPress extensions.
 *
 * Implemented by Plugin and Theme — not by Built_In.
 * Provides a uniform surface for Installable_Strategy template methods.
 *
 * @since 1.0.0
 */
interface Installable {

	/**
	 * Whether this extension is available on WordPress.org.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_wporg(): bool;

	/**
	 * Gets the WordPress.org slug used for plugins_api() lookups, or null if not on WordPress.org.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_wporg_slug(): ?string;

	/**
	 * Whether this extension is currently installed on disk.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_installed(): bool;

	/**
	 * Gets the currently installed version of this extension, or null if not installed.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_installed_version(): ?string;

	/**
	 * Builds the complete update data array for this feature type.
	 *
	 * Each type includes common fields plus type-specific fields (e.g. plugin_file,
	 * installed_version) so the handler does not need an extra feature lookup.
	 * The `package` field is populated by calling the URL builder's build() method
	 * with the feature slug.
	 *
	 * @since 1.0.0
	 *
	 * @param Catalog_Feature      $catalog_feature The catalog entry providing version metadata.
	 * @param Download_Url_Builder $url_builder     Builder for download URLs.
	 *
	 * @return array<string, mixed>
	 */
	public function get_update_data( Catalog_Feature $catalog_feature, Download_Url_Builder $url_builder ): array;
}
