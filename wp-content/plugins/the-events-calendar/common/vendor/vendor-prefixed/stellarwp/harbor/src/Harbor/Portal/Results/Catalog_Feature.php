<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Portal\Results;

use TEC\Common\LiquidWeb\Harbor\Utils\Cast;

/**
 * A single feature entry from the product catalog.
 *
 * Immutable value object hydrated from the Commerce Portal catalog API response.
 *
 * @since 1.0.0
 *
 * @phpstan-type FeatureAttributes array{
 *     slug: string,
 *     kind: string,
 *     minimum_tier: string,
 *     top_dir: ?string,
 *     main_file: ?string,
 *     wporg_slug: ?string,
 *     version: ?string,
 *     release_date: ?string,
 *     changelog: ?string,
 *     name: string,
 *     description: string,
 *     category: string,
 *     authors: ?list<string>,
 *     documentation_url: string,
 *     homepage: ?string,
 * }
 */
final class Catalog_Feature {

	/**
	 * The feature attributes.
	 *
	 * @since 1.0.0
	 *
	 * @var FeatureAttributes
	 */
	protected array $attributes = [
		'slug'              => '',
		'kind'              => '',
		'minimum_tier'      => '',
		'top_dir'           => null,
		'main_file'         => null,
		'wporg_slug'        => null,
		'version'           => null,
		'release_date'      => null,
		'changelog'         => null,
		'name'              => '',
		'description'       => '',
		'category'          => '',
		'authors'           => null,
		'documentation_url' => '',
		'homepage'          => null,
	];

	/**
	 * Constructor for a Catalog_Feature.
	 *
	 * @since 1.0.0
	 *
	 * @phpstan-param FeatureAttributes $attributes
	 *
	 * @param array $attributes The feature attributes.
	 *
	 * @return void
	 */
	public function __construct( array $attributes ) {
		$this->attributes = array_merge( $this->attributes, $attributes );
	}

	/**
	 * Creates a Catalog_Feature from a raw data array.
	 *
	 * The API sends `top_dir` and `main_file` as separate fields; both are stored
	 * verbatim and `get_plugin_file()` combines them on demand into the WordPress
	 * plugin identifier.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $data The feature data.
	 *
	 * @return self
	 */
	public static function from_array( array $data ): self {
		return new self(
			[
				'slug'              => Cast::to_string( $data['slug'] ?? '' ),
				'kind'              => Cast::to_string( $data['kind'] ?? '' ),
				'minimum_tier'      => Cast::to_string( $data['minimum_tier'] ?? '' ),
				'top_dir'           => isset( $data['top_dir'] ) ? Cast::to_string( $data['top_dir'] ) : null,
				'main_file'         => isset( $data['main_file'] ) ? Cast::to_string( $data['main_file'] ) : null,
				'wporg_slug'        => isset( $data['wporg_slug'] ) ? Cast::to_string( $data['wporg_slug'] ) : null,
				'version'           => isset( $data['version'] ) ? Cast::to_string( $data['version'] ) : null,
				'release_date'      => isset( $data['release_date'] ) ? Cast::to_string( $data['release_date'] ) : null,
				'changelog'         => isset( $data['changelog'] ) ? Cast::to_string( $data['changelog'] ) : null,
				'name'              => Cast::to_string( $data['name'] ?? '' ),
				'description'       => Cast::to_string( $data['description'] ?? '' ),
				'category'          => Cast::to_string( $data['category'] ?? '' ),
				'authors'           => isset( $data['authors'] ) && is_array( $data['authors'] )
					? array_map( [ Cast::class, 'to_string' ], array_values( $data['authors'] ) )
					: null,
				'documentation_url' => Cast::to_string( $data['documentation_url'] ?? '' ),
				'homepage'          => isset( $data['homepage'] ) ? Cast::to_string( $data['homepage'] ) : null,
			]
		);
	}

	/**
	 * Converts the feature to an associative array.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		return array_merge(
			$this->attributes,
			[ 'plugin_file' => $this->get_plugin_file() ]
		);
	}

	/**
	 * Gets the feature slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return $this->attributes['slug'];
	}

	/**
	 * Gets the feature kind (plugin or theme).
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_kind(): string {
		return $this->attributes['kind'];
	}

	/**
	 * Gets the minimum tier required for this feature.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_minimum_tier(): string {
		return $this->attributes['minimum_tier'];
	}

	/**
	 * Gets the top-level plugin directory, or null if not applicable.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_top_dir(): ?string {
		return $this->attributes['top_dir'];
	}

	/**
	 * Gets the plugin main file name (without directory), or null if not applicable.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_main_file(): ?string {
		return $this->attributes['main_file'];
	}

	/**
	 * Gets the WordPress plugin identifier — `top_dir/main_file` — or null if either is missing.
	 *
	 * Derived from the separate `top_dir` and `main_file` API fields.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_plugin_file(): ?string {
		$top_dir   = $this->attributes['top_dir'];
		$main_file = $this->attributes['main_file'];

		if ( empty( $top_dir ) || empty( $main_file ) ) {
			return null;
		}

		return $top_dir . '/' . $main_file;
	}

	/**
	 * Gets the WordPress.org slug used for plugins_api() lookups, or null if not on WordPress.org.
	 *
	 * When non-null, this feature is available on WordPress.org and the value
	 * is the slug passed to plugins_api() for install/update operations.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_wporg_slug(): ?string {
		return $this->attributes['wporg_slug'];
	}

	/**
	 * Whether the feature is available on WordPress.org.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_wporg(): bool {
		return $this->attributes['wporg_slug'] !== null;
	}

	/**
	 * Gets the latest available version, or null if not provided.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_version(): ?string {
		return $this->attributes['version'];
	}

	/**
	 * Gets the release date, or null if not provided.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_release_date(): ?string {
		return $this->attributes['release_date'];
	}

	/**
	 * Gets the changelog as an HTML string, or null if not provided.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_changelog(): ?string {
		return $this->attributes['changelog'];
	}

	/**
	 * Gets the display name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->attributes['name'];
	}

	/**
	 * Gets the short description.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_description(): string {
		return $this->attributes['description'];
	}

	/**
	 * Gets the category for grouping/filtering.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_category(): string {
		return $this->attributes['category'];
	}

	/**
	 * Gets the author/product names, or null if not applicable for this feature type.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]|null
	 */
	public function get_authors(): ?array {
		return $this->attributes['authors'];
	}

	/**
	 * Gets the documentation URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_documentation_url(): string {
		return $this->attributes['documentation_url'];
	}

	/**
	 * Gets the homepage URL, or null if not provided.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_homepage(): ?string {
		return $this->attributes['homepage'];
	}
}
