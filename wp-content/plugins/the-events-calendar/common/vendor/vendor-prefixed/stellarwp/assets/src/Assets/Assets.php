<?php

namespace TEC\Common\StellarWP\Assets;

use InvalidArgumentException;

class Assets {
	/**
	 * @var ?Assets
	 */
	protected static $instance;

	/**
	 * @var ?Controller
	 */
	protected ?Controller $controller;

	/**
	 * @var array Array of memoized key value pairs.
	 */
	protected array $memoized = [];

	/**
	 * @var string
	 */
	protected string $base_path;

	/**
	 * @var string
	 */
	protected string $assets_url;

	/**
	 * @var string
	 */
	protected string $version;

	/**
	 * Stores all the Assets and it's configurations.
	 *
	 * @var array
	 */
	protected $assets = [];

	/**
	 * Stores the localized scripts for reference.
	 *
	 * @var array
	 */
	protected $localized = [];

	/**
	 * Constructor.
	 *
	 * @param string|null $base_path Base path to the directory.
	 * @param string|null $assets_url Directory to the assets.
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct( ?string $base_path = null, ?string $assets_url = null ) {
		$this->base_path  = $base_path ?: Config::get_path();
		$this->assets_url = $assets_url ?: trailingslashit( get_site_url() . $this->base_path );
		$this->version    = Config::get_version();
		$this->controller = new Controller( $this );
		$this->controller->register();
	}

	/**
	 * Helper method to get the instance object. Alias of ::init().
	 *
	 * @return Assets
	 * @since 1.0.0
	 *
	 */
	public static function instance(): Assets {
		return static::init();
	}

	/**
	 * Singleton instance.
	 *
	 * @return Assets
	 * @since 1.0.0
	 *
	 */
	public static function init(): Assets {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new self();
		}

		return static::$instance;
	}

	/**
	 * Create an asset.
	 *
	 * @param string $slug The asset slug.
	 * @param string $file The asset file path.
	 * @param string|null $version The asset version.
	 * @param string|null $plugin_path The path to the root of the plugin.
	 */
	public static function asset( string $slug, string $file, string $version = null, string $plugin_path = null ) {
		return static::init()->add( new Asset( $slug, $file, $version, $plugin_path ) );
	}

	/**
	 * Register an Asset and attach a callback to the required action to display it correctly.
	 *
	 * @param Asset $asset Register an asset.
	 *
	 * @return Asset|false The registered object or false on error.
	 * @since 1.0.0
	 *
	 */
	public function add( Asset $asset ) {
		// Prevent weird stuff here.
		$slug = $asset->get_slug();

		if ( $this->exists( $slug ) ) {
			return $this->get( $slug );
		}

		// Set the Asset on the array of notices.
		$this->assets[ $slug ] = $asset;

		// Return the Slug because it might be modified.
		return $asset;
	}

	/**
	 * Checks if an Asset exists.
	 *
	 * @param string|array $slug Slug of the Asset.
	 *
	 * @return bool
	 * @since 1.0.0
	 *
	 */
	public function exists( $slug ) {
		$slug = sanitize_key( $slug );

		return isset( $this->assets[ $slug ] );
	}

	/**
	 * Depending on how certain scripts are loaded and how much cross-compatibility is required we need to be able to
	 * create noConflict backups and restore other scripts, which normally need to be printed directly on the scripts.
	 *
	 * @param string $tag Tag we are filtering.
	 * @param string $handle Which is the ID/Handle of the tag we are about to print.
	 *
	 * @return string Script tag with the before and after strings attached to it.
	 * @since 1.0.0
	 *
	 */
	public function filter_print_before_after_script( $tag, $handle ): string {
		// Only filter for our own filters.
		if ( ! $asset = $this->get( $handle ) ) {
			return (string) $tag;
		}

		// Bail when not dealing with JS assets.
		if ( 'js' !== $asset->get_type() ) {
			return (string) $tag;
		}

		// Only go forward if there is any print before or after.
		if ( empty( $asset->get_print_before() ) && empty( $asset->get_print_after() ) ) {
			return (string) $tag;
		}

		$before       = '';
		$print_before = $asset->get_print_before();
		if ( ! empty( $print_before ) ) {
			$before = (string) ( is_callable( $print_before ) ? call_user_func( $print_before, $asset ) : $print_before );
		}

		$after       = '';
		$print_after = $asset->get_print_after();
		if ( ! empty( $print_after ) ) {
			$after = (string) ( is_callable( $print_after ) ? call_user_func( $print_after, $asset ) : $print_after );
		}

		$tag = $before . (string) $tag . $after;

		return $tag;
	}

	/**
	 * Get the Asset Object configuration.
	 *
	 * @param string|array $slug Slug of the Asset.
	 * @param boolean $sort If we should do any sorting before returning.
	 *
	 * @return array|Asset Array of asset objects, single asset object, or null if looking for a single asset but
	 *                           it was not in the array of objects.
	 * @since 1.0.0
	 *
	 */
	public function get( $slug = null, $sort = true ) {
		$obj = $this;

		if ( is_null( $slug ) ) {
			if ( $sort ) {
				$cache_key_count = __METHOD__ . ':count';
				// Sorts by priority.
				$cache_count = $this->get_var( $cache_key_count, 0 );
				$count       = count( $this->assets );

				if ( $count !== $cache_count ) {
					uasort( $this->assets, static function ( $a, $b ) use ( $obj ) {
						return $obj->sort_by_priority( $a, $b, 'get_priority' );
					} );
					$this->set_var( $cache_key_count, $count );
				}
			}

			return $this->assets;
		}

		// If slug is an array we return all of those.
		if ( is_array( $slug ) ) {
			$assets = [];
			foreach ( $slug as $asset_slug ) {
				$asset_slug = sanitize_key( $asset_slug );
				// Skip empty assets.
				if ( empty( $this->assets[ $asset_slug ] ) ) {
					continue;
				}

				$assets[ $asset_slug ] = $this->assets[ $asset_slug ];
			}

			if ( empty( $assets ) ) {
				return [];
			}

			if ( $sort ) {
				// Sorts by priority.
				uasort( $assets, static function ( $a, $b ) use ( $obj ) {
					return $obj->sort_by_priority( $a, $b, 'get_priority' );
				} );
			}

			return $assets;
		}

		// Prevent weird stuff here.
		$slug = sanitize_key( $slug );

		if ( ! empty( $this->assets[ $slug ] ) ) {
			return $this->assets[ $slug ];
		}

		return [];
	}

	/**
	 * Gets a memoized value.
	 *
	 * @param string $var Var name.
	 * @param mixed|null $default Default value.
	 *
	 * @return mixed|null
	 */
	public function get_var( string $var, $default = null ) {
		return $this->memoized[ $var ] ?? $default;
	}

	/**
	 * Sorting function based on Priority
	 *
	 * @param object|array $b Second subject to compare.
	 * @param object|array $a First Subject to compare.
	 * @param string $method Method to use for sorting.
	 *
	 * @return int
	 * @since 1.0.0
	 *
	 */
	public function sort_by_priority( $a, $b, $method = null ) {
		if ( is_array( $a ) ) {
			$a_priority = $a['priority'];
		} else {
			$a_priority = $method ? $a->$method() : $a->priority;
		}

		if ( is_array( $b ) ) {
			$b_priority = $b['priority'];
		} else {
			$b_priority = $method ? $b->$method() : $b->priority;
		}

		if ( (int) $a_priority === (int) $b_priority ) {
			return 0;
		}

		return (int) $a_priority > (int) $b_priority ? 1 : - 1;
	}

	/**
	 * Sets a memoized value.
	 *
	 * @param string $var Var name.
	 * @param mixed|null $value The value.
	 */
	public function set_var( string $var, $value = null ) {
		$this->memoized[ $var ] = $value;
	}

	/**
	 * Handles adding localization data, when attached to `script_loader_tag` which allows dependencies to load in their
	 * localization data as well.
	 *
	 * @param string $tag Tag we are filtering.
	 * @param string $handle Which is the ID/Handle of the tag we are about to print.
	 *
	 * @return string Script tag with the localization variable HTML attached to it.
	 * @since 1.0.0
	 *
	 */
	public function filter_add_localization_data( $tag, $handle ) {
		// Only filter for own filters.
		if ( ! $asset = $this->get( $handle ) ) {
			return $tag;
		}

		// Bail when not dealing with JS assets.
		if ( 'js' !== $asset->get_type() ) {
			return $tag;
		}

		$localize_scripts        = $asset->get_localize_scripts();
		$custom_localize_scripts = $asset->get_custom_localize_scripts();

		// Only localize on JS and if we have data.
		if ( empty( $localize_scripts ) && empty( $custom_localize_scripts ) ) {
			return $tag;
		}

		$localization_html = '';

		if ( count( $localize_scripts ) ) {
			global $wp_scripts;

			$localization = $localize_scripts;

			/**
			 * Check to ensure we haven't already localized it before.
			 *
			 * @since 1.0.0
			 */
			foreach ( $localization as $key => $localize ) {

				if ( in_array( $key, $this->localized ) ) {
					continue;
				}

				// If we have a Callable as the Localize data we execute it.
				if ( is_callable( $localize ) ) {
					$localize = $localize( $asset );
				}

				wp_localize_script( $asset->get_slug(), $key, $localize );

				$this->localized[] = $key;
			}

			// Fetch the HTML for all the localized data.
			ob_start();
			$wp_scripts->print_extra_script( $asset->get_slug(), true );
			$localization_html = ob_get_clean();

			// After printing it remove data;|
			$wp_scripts->add_data( $asset->get_slug(), 'data', '' );
		}

		/*
		 * Print the dot.notation namespaced objects for the asset.
		 */
		foreach ( $custom_localize_scripts as [$object_name, $data] ) {
			// If we have a Callable as the Localize data we execute it.
			if ( is_callable( $data ) ) {
				$data = $data( $asset );
			}

			$localized_key = "{$asset->get_slug()}::{$object_name}";

			if ( in_array( $localized_key, $this->localized, true ) ) {
				continue;
			}

			$frags    = explode( '.', $object_name );
			$js_data  = '';
			$var_name = '';
			foreach ( $frags as $i => $frag ) {
				$var_name = ltrim( $var_name . '.' . $frag, '.' );
				if ( isset( $frags[ $i + 1 ] ) ) {
					$js_data   .= PHP_EOL . sprintf( 'window.%1$s = window.%1$s || {};', $var_name );
				} else {
					$json_data = wp_json_encode( $data );
					$js_data .= PHP_EOL . sprintf( 'window.%1$s = Object.assign(window.%1$s || {}, %2$s);', $var_name, $json_data );
				}
			}

			$localization_html .= sprintf(
				'<script id="%s-ns-extra">%s' . PHP_EOL . '</script>',
				$asset->get_slug(),
				$js_data
			);

			$this->localized[] = $localized_key;
		}

		return $localization_html . $tag;
	}

	/**
	 * Filters the Script tags to attach Async and/or Defer based on the rules we set in our Asset class.
	 *
	 * @param string $tag Tag we are filtering.
	 * @param string $handle Which is the ID/Handle of the tag we are about to print.
	 *
	 * @return string Script tag with the defer and/or async attached.
	 * @since 1.0.0
	 *
	 */
	public function filter_tag_async_defer( $tag, $handle ) {
		// Only filter for our own filters.
		if ( ! $asset = $this->get( $handle ) ) {
			return $tag;
		}

		// Bail when not dealing with JS assets.
		if ( 'js' !== $asset->get_type() ) {
			return $tag;
		}

		// When async and defer are false we bail with the tag.
		if ( ! $asset->is_deferred() && ! $asset->is_async() ) {
			return $tag;
		}

		$tag_has_async = false !== strpos( $tag, ' async ' );
		$tag_has_defer = false !== strpos( $tag, ' defer ' );
		$replacement   = '<script ';

		if ( $asset->is_async() && ! $tag_has_async ) {
			$replacement .= 'async ';
		}

		if ( $asset->is_deferred() && ! $tag_has_defer ) {
			$replacement .= 'defer ';
		}


		return str_replace( '<script ', $replacement, $tag );
	}

	/**
	 * Filters the Script tags to attach type=module based on the rules we set in our Asset class.
	 *
	 * @since 1.0.0
	 * @since 1.2.6
	 *
	 * @param string $tag    Tag we are filtering.
	 * @param string $handle Which is the ID/Handle of the tag we are about to print.
	 *
	 * @return string Script tag with the type=module
	 */
	public function filter_modify_to_module( $tag, $handle ) {
		$asset = $this->get( $handle );
		// Only filter for our own filters.
		if ( ! $asset ) {
			return $tag;
		}

		// Bail when not dealing with JS assets.
		if ( 'js' !== $asset->get_type() ) {
			return $tag;
		}

		// When not module we bail with the tag.
		if ( ! $asset->is_module() ) {
			return $tag;
		}

		// Remove the type attribute if it exists.
		preg_match( "/ *type=['\"]{0,1}[^'\"]+['\"]{0,1}/i", $tag, $matches );
		if ( ! empty( $matches ) ) {
			$tag = str_replace( $matches[0], '', $tag );
		}

		$replacement = '<script type="module" ';

		return str_replace( '<script', $replacement, $tag );
	}

	/**
	 * Enqueues registered assets based on their groups.
	 *
	 * @param string|array $groups Which groups will be enqueued.
	 * @param bool $should_enqueue_no_matter_what Whether to ignore conditional requirements when enqueuing.
	 *
	 * @since 1.0.0
	 *
	 * @uses  Assets::enqueue()
	 *
	 */
	public function enqueue_group( $groups, bool $should_enqueue_no_matter_what = false ) {
		$assets  = $this->get( null, false );
		$enqueue = [];

		foreach ( $assets as $asset ) {
			if ( empty( $asset->get_groups() ) ) {
				continue;
			}

			$intersect = array_intersect( (array) $groups, $asset->get_groups() );

			if ( empty( $intersect ) ) {
				continue;
			}
			$enqueue[] = $asset->get_slug();
		}

		$this->enqueue( $enqueue, $should_enqueue_no_matter_what );
	}

	/**
	 * Enqueues registered assets.
	 *
	 * This method is called on whichever action (if any) was declared during registration.
	 *
	 * It can also be called directly with a list of asset slugs to forcibly enqueue, which may be
	 * useful where an asset is required in a situation not anticipated when it was originally
	 * registered.
	 *
	 * @param string|array $assets_to_enqueue Which assets will be enqueued.
	 * @param bool $should_enqueue_no_matter_what Whether to ignore conditional requirements when enqueuing.
	 *
	 * @since 1.0.0
	 *
	 */
	public function enqueue( $assets_to_enqueue = null, bool $should_enqueue_no_matter_what = false ) {
		$assets_to_enqueue = array_filter( (array) $assets_to_enqueue );
		if ( ! empty( $assets_to_enqueue ) ) {
			$assets = (array) $this->get( $assets_to_enqueue );
		} else {
			$assets = $this->get();
		}

		foreach ( $assets as $asset ) {
			$slug = $asset->get_slug();

			// Should this asset be enqueued regardless of the current filter/any conditional requirements?
			$must_enqueue = in_array( $slug, $assets_to_enqueue );
			$actions      = $asset->get_action();

			if ( empty( $actions ) && $must_enqueue ) {
				$this->do_enqueue( $asset, $must_enqueue );
			}

			foreach ( $asset->get_action() as $action ) {
				$in_filter  = current_filter() === $action;
				$did_action = did_action( $action ) > 0;

				// Skip if we are not on the correct filter (unless we are forcibly enqueuing).
				if ( ! $in_filter && ! $must_enqueue && ! $did_action ) {
					continue;
				}

				// If any single conditional returns true, then we need to enqueue the asset.
				if ( empty( $action ) && ! $must_enqueue ) {
					continue;
				}

				$this->do_enqueue( $asset, $should_enqueue_no_matter_what );
			}
		}
	}

	/**
	 * Enqueues registered assets.
	 *
	 * This method is called on whichever action (if any) was declared during registration.
	 *
	 * It can also be called directly with a list of asset slugs to forcibly enqueue, which may be
	 * useful where an asset is required in a situation not anticipated when it was originally
	 * registered.
	 *
	 * @param Asset $asset Asset to enqueue.
	 * @param bool $force_enqueue Whether to ignore conditional requirements when enqueuing.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function do_enqueue( Asset $asset, bool $force_enqueue = false ): void {
		$hook_prefix = Config::get_hook_prefix();
		$slug        = $asset->get_slug();

		// If this asset was late called
		if ( ! $asset->is_registered() ) {
			$this->register_in_wp( $asset );
		}

		if ( $asset->is_enqueued() ) {
			return;
		}

		// Default to enqueuing the asset if there are no conditionals,
		// and default to not enqueuing it if there *are* conditionals.
		$condition     = $asset->get_condition();
		$has_condition = ! empty( $condition );
		$enqueue       = ! $has_condition;

		if ( $has_condition ) {
			$enqueue = (bool) call_user_func( $condition );
		}

		/**
		 * Allows developers to hook-in and prevent an asset from being loaded.
		 *
		 * @param bool $enqueue If we should enqueue or not a given asset.
		 * @param object $asset Which asset we are dealing with.
		 *
		 * @since 1.0.0
		 *
		 */
		$enqueue = (bool) apply_filters( "stellarwp/assets/{$hook_prefix}/enqueue", $enqueue, $asset );

		/**
		 * Allows developers to hook-in and prevent an asset from being loaded.
		 *
		 * @param bool $enqueue If we should enqueue or not a given asset.
		 * @param object $asset Which asset we are dealing with.
		 *
		 * @since 1.0.0
		 *
		 */
		$enqueue = (bool) apply_filters( "stellarwp/assets/{$hook_prefix}/enqueue_{$slug}", $enqueue, $asset );

		if ( ! $enqueue && ! $force_enqueue ) {
			return;
		}

		if ( 'js' === $asset->get_type() ) {
			if ( $asset->should_print() && ! $asset->is_printed() ) {
				$asset->set_as_printed();
				wp_print_scripts( [ $slug ] );
			}
			// We print first, and tell the system it was enqueued, WP is smart not to do it twice.
			wp_enqueue_script( $slug );
		} else {
			if ( $asset->should_print() && ! $asset->is_printed() ) {
				$asset->set_as_printed();
				wp_print_styles( [ $slug ] );
			}

			// We print first, and tell the system it was enqueued, WP is smart not to do it twice.
			wp_enqueue_style( $slug );

			$style_data = $asset->get_style_data();
			foreach ( $style_data as $key => $value ) {
				wp_style_add_data( $slug, $key, $value );
			}
		}

		if ( ! empty( $asset->get_after_enqueue() ) && is_callable( $asset->get_after_enqueue() ) ) {
			call_user_func_array( $asset->get_after_enqueue(), [ $asset ] );
		}

		$asset->set_as_enqueued();
	}

	/**
	 * Register the Assets on the correct hooks.
	 *
	 * @since 1.0.0
	 * @since 1.4.5 Ensure the method accepts only `null` or an `Asset` instance or an array of `Asset[]` instances.
	 *
	 * @param Asset[]|Asset|null $assets Array of asset objects, single asset object, or null.
	 *
	 * @return void
	 */
	public function register_in_wp( $assets = null ) {
		if ( ! (
			did_action( 'init' ) || did_action( 'wp_enqueue_scripts' )
			|| did_action( 'admin_enqueue_scripts' ) || did_action( 'login_enqueue_scripts' )
		)
		) {
			// Registering the asset now would trigger a doing_it_wrong notice: queue the assets to be registered later.
			if ( $assets === null || empty( $assets ) ) {
				return;
			}

			if ( ! is_array( $assets ) ) {
				$assets = [ $assets ];
			}

			foreach ( $assets as $asset ) {
				if ( ! $asset instanceof Asset ) {
					throw new InvalidArgumentException( 'Assets in register_in_wp() must be of type Asset' );
				}

				// Register later, avoid the doing_it_wrong notice.
				$this->assets[ $asset->get_slug() ] = $asset;
			}

			return;
		}

		if ( null === $assets ) {
			$assets = $this->get();
		}

		if ( ! is_array( $assets ) ) {
			$assets = [ $assets ];
		}

		if ( empty( $assets ) ) {
			return;
		}

		foreach ( $assets as $asset ) {
			if ( ! $asset instanceof Asset ) {
				throw new InvalidArgumentException( 'Assets in register_in_wp() must be of type Asset' );
			}

			// Asset is already registered.
			if ( $asset->is_registered() ) {
				continue;
			}

			$asset_slug = $asset->get_slug();

			if ( 'js' === $asset->get_type() ) {
				// Script is already registered.
				if ( wp_script_is( $asset_slug, 'registered' ) ) {
					continue;
				}

				wp_register_script( $asset_slug, $asset->get_url(), $asset->get_dependencies(), $asset->get_version(), $asset->is_in_footer() );

				// Register that this asset is actually registered on the WP methods.
				// @phpstan-ignore-next-line
				if ( wp_script_is( $asset_slug, 'registered' ) ) {
					$asset->set_as_registered();
				}

				if (
					! empty( $asset->get_translation_path() )
					&& ! empty( $asset->get_textdomain() )
				) {
					wp_set_script_translations( $asset_slug, $asset->get_textdomain(), $asset->get_translation_path() );
				}
			} else {
				// Style is already registered.
				if ( wp_style_is( $asset_slug, 'registered' ) ) {
					continue;
				}

				wp_register_style( $asset_slug, $asset->get_url(), $asset->get_dependencies(), $asset->get_version(), $asset->get_media() );

				// Register that this asset is actually registered on the WP methods.
				// @phpstan-ignore-next-line
				if ( wp_style_is( $asset_slug, 'registered' ) ) {
					$asset->set_as_registered();
				}

				$style_data = $asset->get_style_data();
				if ( $style_data ) {
					foreach ( $style_data as $datum_key => $datum_value ) {
						wp_style_add_data( $asset_slug, $datum_key, $datum_value );
					}
				}
			}

			// If we don't have an action we don't even register the action to enqueue.
			if ( empty( $asset->get_action() ) ) {
				continue;
			}

			// Now add an action to enqueue the registered assets.
			foreach ( (array) $asset->get_action() as $action ) {
				// Enqueue the registered assets at the appropriate time.
				if ( did_action( $action ) > 0 ) {
					$this->enqueue();
				} else {
					add_action( $action, [ $this, 'enqueue' ], $asset->get_priority(), 0 );
				}
			}
		}
	}

	/**
	 * Removes an Asset from been registered and enqueue.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the Asset.
	 *
	 * @return bool
	 */
	public function remove( $slug ) {
		if ( ! $this->exists( $slug ) ) {
			return false;
		}

		$type = $this->get( $slug )->get_type();

		if ( $type === 'css' ) {
			wp_dequeue_style( $slug );
			wp_deregister_style( $slug );
		} else {
			wp_dequeue_script( $slug );
			wp_deregister_script( $slug );
		}

		unset( $this->assets[ $slug ] );

		return true;
	}

	/**
	 * Prints the `script` (JS) and `link` (CSS) HTML tags associated with one or more assets groups.
	 *
	 * The method will force the scripts and styles to print overriding their registration and conditional.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $group Which group(s) should be enqueued.
	 * @param bool $echo Whether to print the group(s) tag(s) to the page or not; default to `true` to
	 *                            print the HTML `script` (JS) and `link` (CSS) tags to the page.
	 *
	 * @return string The `script` and `link` HTML tags produced for the group(s).
	 */
	public function print_group( $group, $echo = true ) {
		$all_assets = $this->get();
		$groups     = (array) $group;
		$to_print   = array_filter( $all_assets, static function ( Asset $asset ) use ( $groups ) {
			$asset_groups = $asset->get_groups();

			return ! empty( $asset_groups ) && array_intersect( $asset_groups, $groups );
		} );
		$by_type    = array_reduce( $to_print, static function ( array $acc, Asset $asset ) {
			$acc[ $asset->get_type() ][] = $asset->get_slug();

			return $acc;
		}, [ 'css' => [], 'js' => [] ] );


		// Make sure each script is registered.
		foreach ( $to_print as $slug => $asset ) {
			if ( $asset->is_registered() ) {
				continue;
			}
			'js' === $asset->get_type()
				? wp_register_script( $slug, $asset->get_file(), $asset->get_dependencies(), $asset->get_version() )
				: wp_register_style( $slug, $asset->get_file(), $asset->get_dependencies(), $asset->get_version() );
		}

		ob_start();
		wp_scripts()->do_items( $by_type['js'] );
		wp_styles()->do_items( $by_type['css'] );
		$tags = ob_get_clean();

		if ( $echo ) {
			echo $tags;
		}

		return $tags;
	}

}
