<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\CLI\Commands;

use TEC\Common\LiquidWeb\Harbor\Features\Feature_Collection;
use TEC\Common\LiquidWeb\Harbor\Features\Manager;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Feature as Feature_Type;
use TEC\Common\LiquidWeb\Harbor\CLI\Display;
use TEC\Common\LiquidWeb\Harbor\Utils\Cast;
use WP_CLI;
use WP_CLI\Formatter;
use WP_CLI_Command;

/**
 * Manage Harbor features.
 *
 * ## EXAMPLES
 *
 *     # List all features
 *     wp harbor feature list
 *
 *     # List available features as JSON
 *     wp harbor feature list --available=true --format=json
 *
 *     # Count features for a product
 *     wp harbor feature list --product=Kadence --format=count
 *
 *     # Get a single feature
 *     wp harbor feature get my-feature
 *
 *     # Check if a feature is enabled
 *     wp harbor feature is-enabled my-feature
 *
 *     # Enable a feature by slug
 *     wp harbor feature enable my-feature
 *
 *     # Disable a feature by slug
 *     wp harbor feature disable my-feature
 *
 *     # Update a feature to the latest version
 *     wp harbor feature update my-feature
 *
 * @since 1.0.0
 */
class Feature extends WP_CLI_Command {

	/**
	 * Default fields shown in table output.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private const DEFAULT_FIELDS = 'slug,name,type,product,is_available,is_enabled';

	/**
	 * The feature manager instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Manager
	 */
	private Manager $manager;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Manager $manager The feature manager.
	 */
	public function __construct( Manager $manager ) {
		$this->manager = $manager;
	}

	/**
	 * Lists features with optional filters.
	 *
	 * ## OPTIONS
	 *
	 * [--product=<product>]
	 * : Filter by product.
	 *
	 * [--tier=<tier>]
	 * : Filter by tier.
	 *
	 * [--available=<available>]
	 * : Filter by availability (true or false).
	 *
	 * [--type=<type>]
	 * : Filter by feature type (plugin, theme).
	 *
	 * [--fields=<fields>]
	 * : Comma-separated list of fields to display.
	 *
	 * [--format=<format>]
	 * : Output format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - json
	 *   - csv
	 *   - yaml
	 *   - count
	 * ---
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields are available for display:
	 *
	 * * slug
	 * * name
	 * * description
	 * * type
	 * * product
	 * * tier
	 * * is_available
	 * * is_enabled
	 * * documentation_url
	 * * installed_version (plugin, theme)
	 * * release_date (plugin, theme)
	 * * plugin_file (plugin)
	 * * wporg_slug (plugin, theme)
	 *
	 * ## EXAMPLES
	 *
	 *     # List all features in a table
	 *     wp harbor feature list
	 *
	 *     # List available features as JSON
	 *     wp harbor feature list --available=true --format=json
	 *
	 *     # Count features for a product
	 *     wp harbor feature list --product=Kadence --format=count
	 *
	 * @subcommand list
	 *
	 * @param array<int, string>    $args       Positional arguments.
	 * @param array<string, string> $assoc_args Associative arguments.
	 *
	 * @return void
	 */
	public function list_( array $args, array $assoc_args ): void {
		$features = $this->manager->get_all();

		if ( is_wp_error( $features ) ) {
			WP_CLI::error( $features->get_error_message() );

			return; // WP_CLI::error() exits, but PHPStan needs this for type narrowing.
		}

		$product   = $assoc_args['product'] ?? null;
		$tier      = $assoc_args['tier'] ?? null;
		$available = isset( $assoc_args['available'] ) ? Cast::to_bool( $assoc_args['available'] ) : null;
		$type      = $assoc_args['type'] ?? null;

		if ( $product !== null || $tier !== null || $available !== null || $type !== null ) {
			$features = $features->filter( $product, $tier, $available, $type );
		}

		$items = $this->collection_to_display_items( $features );

		$formatter = new Formatter(
			$assoc_args,
			explode( ',', self::DEFAULT_FIELDS )
		);

		$formatter->display_items( $items );
	}

	/**
	 * Gets a single feature by slug.
	 *
	 * ## OPTIONS
	 *
	 * <slug>
	 * : The feature slug.
	 *
	 * [--fields=<fields>]
	 * : Comma-separated list of fields to display.
	 *
	 * [--format=<format>]
	 * : Output format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - json
	 *   - csv
	 *   - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     # Show feature details
	 *     wp harbor feature get my-feature
	 *
	 *     # Get feature as JSON
	 *     wp harbor feature get my-feature --format=json
	 *
	 * @param array<int, string>    $args       Positional arguments.
	 * @param array<string, string> $assoc_args Associative arguments.
	 *
	 * @return void
	 */
	public function get( array $args, array $assoc_args ): void {
		$slug    = $args[0];
		$feature = $this->manager->get( $slug );

		if ( ! $feature ) {
			WP_CLI::error( sprintf( 'Feature "%s" not found.', $slug ) );

			return; // WP_CLI::error() exits, but PHPStan needs this for type narrowing.
		}

		$item = $this->feature_to_display_item( $feature );

		$fields = isset( $assoc_args['fields'] )
			? explode( ',', $assoc_args['fields'] )
			: array_keys( $item );

		$formatter = new Formatter( $assoc_args, $fields );
		$formatter->display_item( $item );
	}

	/**
	 * Checks whether a feature is currently enabled.
	 *
	 * Exits with code 0 if the feature is enabled, 1 if not enabled or not found.
	 * Useful in shell scripts: `if wp harbor feature is-enabled my-feature; then ...`
	 *
	 * ## OPTIONS
	 *
	 * <slug>
	 * : The feature slug.
	 *
	 * ## EXAMPLES
	 *
	 *     # Check if a feature is enabled (exit code 0 = enabled)
	 *     wp harbor feature is-enabled my-feature
	 *
	 *     # Use in a script
	 *     if wp harbor feature is-enabled my-feature; then
	 *       echo "Feature is enabled"
	 *     fi
	 *
	 * @subcommand is-enabled
	 *
	 * @param array<int, string>    $args       Positional arguments.
	 * @param array<string, string> $assoc_args Associative arguments.
	 *
	 * @return void
	 */
	public function is_enabled( array $args, array $assoc_args ): void {
		$slug   = $args[0];
		$result = $this->manager->is_enabled( $slug );

		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );

			return; // WP_CLI::error() exits, but PHPStan needs this for type narrowing.
		}

		if ( $result ) {
			WP_CLI::log( sprintf( 'Feature "%s" is enabled.', $slug ) );
		} else {
			WP_CLI::error( sprintf( 'Feature "%s" is not enabled.', $slug ) );
		}
	}

	/**
	 * Enables a feature.
	 *
	 * ## OPTIONS
	 *
	 * <slug>
	 * : The feature slug. Use `wp harbor feature list` to see available slugs.
	 *
	 * ## EXAMPLES
	 *
	 *     # Enable a feature
	 *     wp harbor feature enable my-feature
	 *
	 * @param array<int, string>    $args       Positional arguments.
	 * @param array<string, string> $assoc_args Associative arguments.
	 *
	 * @return void
	 */
	public function enable( array $args, array $assoc_args ): void {
		$slug    = $args[0];
		$feature = $this->manager->enable( $slug );

		if ( is_wp_error( $feature ) ) {
			WP_CLI::error( $feature->get_error_message() );
		} else {
			WP_CLI::success( sprintf( 'Feature "%s" enabled.', $slug ) );
		}
	}

	/**
	 * Disables a feature.
	 *
	 * ## OPTIONS
	 *
	 * <slug>
	 * : The feature slug. Use `wp harbor feature list` to see available slugs.
	 *
	 * ## EXAMPLES
	 *
	 *     # Disable a feature
	 *     wp harbor feature disable my-feature
	 *
	 * @param array<int, string>    $args       Positional arguments.
	 * @param array<string, string> $assoc_args Associative arguments.
	 *
	 * @return void
	 */
	public function disable( array $args, array $assoc_args ): void {
		$slug    = $args[0];
		$feature = $this->manager->disable( $slug );

		if ( is_wp_error( $feature ) ) {
			WP_CLI::error( $feature->get_error_message() );
		} else {
			WP_CLI::success( sprintf( 'Feature "%s" disabled.', $slug ) );
		}
	}

	/**
	 * Updates a feature to the latest version.
	 *
	 * For plugin and theme features, this upgrades the installed version to the
	 * latest available from the catalog.
	 *
	 * ## OPTIONS
	 *
	 * <slug>
	 * : The feature slug. Use `wp harbor feature list` to see available slugs.
	 *
	 * ## EXAMPLES
	 *
	 *     # Update a feature
	 *     wp harbor feature update my-feature
	 *
	 * @param array<int, string>    $args       Positional arguments.
	 * @param array<string, string> $assoc_args Associative arguments.
	 *
	 * @return void
	 */
	public function update( array $args, array $assoc_args ): void {
		$slug    = $args[0];
		$feature = $this->manager->update( $slug );

		if ( is_wp_error( $feature ) ) {
			WP_CLI::error( $feature->get_error_message() );
		} else {
			WP_CLI::success( sprintf( 'Feature "%s" updated.', $slug ) );
		}
	}

	/**
	 * Converts a feature collection to display items with boolean casting.
	 *
	 * @since 1.0.0
	 *
	 * @param Feature_Collection $features The feature collection.
	 *
	 * @return list<array<string, mixed>>
	 */
	private function collection_to_display_items( Feature_Collection $features ): array {
		$items = [];

		foreach ( $features as $feature ) {
			$items[] = $this->feature_to_display_item( $feature );
		}

		return $items;
	}

	/**
	 * Converts a single feature to a display-ready associative array.
	 *
	 * Booleans are cast to 'true'/'false' strings for table display.
	 * The is_enabled field is resolved via the Manager.
	 *
	 * @since 1.0.0
	 *
	 * @param Feature_Type $feature The feature instance.
	 *
	 * @return array<string, mixed>
	 */
	private function feature_to_display_item( Feature_Type $feature ): array {
		$item = $feature->to_array();

		$item['is_available'] = Display::bool( ! empty( $item['is_available'] ) );
		$item['is_enabled']   = Display::bool( ! empty( $item['is_enabled'] ) );
		$item['wporg_slug'] ??= '';

		foreach ( $item as $key => $value ) {
			if ( is_array( $value ) ) {
				$item[ $key ] = implode( ', ', array_map( [ Cast::class, 'to_string' ], $value ) );
			}
		}

		return $item;
	}
}
