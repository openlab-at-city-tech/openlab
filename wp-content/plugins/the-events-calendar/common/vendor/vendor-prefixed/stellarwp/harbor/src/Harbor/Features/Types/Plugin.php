<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features\Types;

use TEC\Common\LiquidWeb\Harbor\Portal\Contracts\Download_Url_Builder;
use TEC\Common\LiquidWeb\Harbor\Portal\Results\Catalog_Feature;
use TEC\Common\LiquidWeb\Harbor\Features\Contracts\Installable;
use TEC\Common\LiquidWeb\Harbor\Utils\Cast;

/**
 * A Feature delivered as a standalone WordPress plugin.
 *
 * The Plugin_Strategy installs the plugin via plugins_api() + Plugin_Upgrader,
 * and uses plugin_file (plugin file path) to activate/deactivate it.
 *
 * @since 1.0.0
 */
final class Plugin extends Feature implements Installable {

	/**
	 * Constructor for a Feature delivered as a standalone WordPress plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $attributes The feature attributes.
	 *
	 * @return void
	 */
	public function __construct( array $attributes ) {
		$attributes['type'] = self::TYPE_PLUGIN;

		$attributes = array_merge(
			$attributes,
			[
				'plugin_file'       => $attributes['plugin_file'] ?? '',
				'wporg_slug'        => $attributes['wporg_slug'] ?? null,
				'release_date'      => $attributes['release_date'] ?? null,
				'installed_version' => $attributes['installed_version'] ?? null,
				'version'           => $attributes['version'] ?? null,
				'changelog'         => $attributes['changelog'] ?? null,
			]
		);

		parent::__construct( $attributes );
	}

	/**
	 * Creates a Feature instance from an associative array.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $data The feature data from the API response.
	 *
	 * @return static
	 */
	public static function from_array( array $data ) {
		return new self( $data );
	}

	/**
	 * Gets the plugin file path relative to the plugins directory
	 * (e.g. "stellar-export/stellar-export.php").
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_plugin_file(): string {
		return Cast::to_string( $this->attributes['plugin_file'] ?? '' );
	}

	/**
	 * Whether this plugin is available on WordPress.org.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_wporg(): bool {
		return ( $this->attributes['wporg_slug'] ?? null ) !== null;
	}

	/**
	 * Gets the WordPress.org slug used for plugins_api() lookups, or null if not on WordPress.org.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_wporg_slug(): ?string {
		$slug = $this->attributes['wporg_slug'] ?? null;

		return $slug !== null ? Cast::to_string( $slug ) : null;
	}

	/**
	 * Builds the complete update data array for this Plugin feature.
	 *
	 * The `package` field is populated by the URL builder using the feature
	 * slug (and any data the implementation needs, e.g. license key and site domain).
	 *
	 * @since 1.0.0
	 *
	 * @param Catalog_Feature      $catalog_feature The catalog entry providing version metadata.
	 * @param Download_Url_Builder $url_builder     Builder for download URLs.
	 *
	 * @return array<string, mixed>
	 */
	public function get_update_data( Catalog_Feature $catalog_feature, Download_Url_Builder $url_builder ): array {
		$installed_version = $this->get_installed_version() ?? '';
		$catalog_version   = $catalog_feature->get_version() ?? '';
		$changelog         = $catalog_feature->get_changelog();

		$sections = [
			'description' => $this->get_description(),
		];

		// WordPress renders each section as a tab in the plugin details modal; only add the changelog when we have one.
		if ( $changelog !== null && $changelog !== '' ) {
			$sections['changelog'] = $changelog;
		}

		return [
			'name'              => $this->get_name(),
			'slug'              => $this->get_slug(),
			'version'           => $catalog_version,
			'package'           => $url_builder->build( $this->get_slug() ),
			'url'               => $this->get_documentation_url(),
			'author'            => '',
			'sections'          => $sections,
			'plugin_file'       => $this->get_plugin_file(),
			'installed_version' => $installed_version,
			'has_update'        => $installed_version !== '' && $catalog_version !== '' && version_compare( $catalog_version, $installed_version, '>' ),
		];
	}

	/**
	 * Gets the plugin directory name derived from the plugin file path.
	 *
	 * For "stellar-export/stellar-export.php" this returns "stellar-export".
	 * Used for filesystem operations.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_plugin_directory(): string {
		return dirname( $this->get_plugin_file() );
	}

	/**
	 * Checks whether this Zip feature's plugin is currently installed.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_installed(): bool {
		$plugin_file = $this->get_plugin_file();

		// TODO: We should throw an error on object construction if plugin_file is not set for Plugin Features.
		if ( empty( $plugin_file ) ) {
			return false;
		}

		return file_exists( trailingslashit( WP_PLUGIN_DIR ) . $plugin_file );
	}

	/**
	 * Gets the currently installed version of this Zip feature's plugin.
	 * Returns null if the plugin is not installed.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_installed_version(): ?string {
		if ( ! $this->is_installed() ) {
			return null;
		}

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php'; // @phpstan-ignore-line -- ABSPATH exists.
		}

		$plugin_data = get_plugin_data( trailingslashit( WP_PLUGIN_DIR ) . $this->get_plugin_file() );

		// get_plugin_data() fills every header key, defaulting to an empty string,
		// so Version is always present and the null coalesce never applied.
		return $plugin_data['Version'];
	}
}
