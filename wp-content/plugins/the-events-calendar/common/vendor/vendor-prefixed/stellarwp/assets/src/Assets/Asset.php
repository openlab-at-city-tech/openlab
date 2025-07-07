<?php

namespace TEC\Common\StellarWP\Assets;

use InvalidArgumentException;

class Asset {
	/**
	 * @var array The asset action.
	 */
	protected array $action = [];

	/**
	 * The asset style data.
	 *
	 * @see: wp_style_add_data()
	 *
	 * @var array
	 */
	protected array $add_style_data = [];

	/**
	 * The callable to execute after enqueuing.
	 *
	 * @var mixed
	 */
	protected $after_enqueue;

	/**
	 * The asset asset file contents.
	 *
	 * @var array
	 */
	protected array $asset_file_contents = [];

	/**
	 * The asset file path.
	 *
	 * @var string
	 */
	protected string $asset_file_path = '';

	/**
	 * The asset conditional callable.
	 *
	 * @var mixed
	 */
	protected $condition;

	/**
	 * An array of objects to localized using dot-notation and namespaces.
	 *
	 * @var array<array{0: string, 1:mixed}>
	 */
	protected array $custom_localize_script_objects = [];

	/**
	 * The asset dependencies.
	 *
	 * @var array<string>|callable
	 */
	protected $dependencies = [];

	/**
	 * The asset file path.
	 *
	 * @var ?string
	 */
	protected ?string $file = null;

	/**
	 * The asset groups.
	 *
	 * This is used for organizing assets into groups.
	 *
	 * @var array
	 */
	protected array $groups = [];

	/**
	 * Should the asset be loaded in the footer?
	 *
	 * @var bool
	 */
	protected bool $in_footer = true;

	/**
	 * Should the asset be marked as async?
	 *
	 * @var bool
	 */
	protected bool $is_async = false;

	/**
	 * Should the asset be marked as deferred?
	 *
	 * @var bool
	 */
	protected bool $is_deferred = false;

	/**
	 * Is the asset enqueued?
	 *
	 * @var bool
	 */
	protected bool $is_enqueued = false;

	/**
	 * Is the asset a module?
	 *
	 * @var bool
	 */
	protected bool $is_module = false;

	/**
	 * Is the asset printed?
	 *
	 * @var bool
	 */
	protected bool $is_printed = false;

	/**
	 * Is the asset registered?
	 *
	 * @var bool
	 */
	protected bool $is_registered = false;

	/**
	 * Is the asset a vendor asset?
	 *
	 * @var bool
	 */
	protected bool $is_vendor = false;

	/**
	 * The asset wp_localize_script objects for this asset.
	 *
	 * @var array<string,mixed>
	 */
	protected array $wp_localize_script_objects = [];

	/**
	 * The asset file media setting.
	 *
	 * @var string
	 */
	protected string $media = 'all';

	/**
	 * The relative path to the minified version of this file.
	 *
	 * @var ?string
	 */
	protected ?string $min_path = null;

	/**
	 * The asset file min url.
	 *
	 * @var ?string
	 */
	protected ?string $min_url = null;

	/**
	 * The relative path to the asset.
	 *
	 * @var ?string
	 */
	protected ?string $path = null;

	/**
	 * The root plugin path for this asset.
	 *
	 * @var string
	 */
	protected string $root_path = '';

	/**
	 * The path group name for this asset.
	 *
	 * A path group is a group of assets that share the same path which could be different that the root path or the asset's path.
	 *
	 * The order of priority goes like this:
	 *
	 * 1. If a specific root path is set, that will be used.
	 * 2. If a path group is set, that will be used.
	 * 3. Otherwise, the root path will be used.
	 *
	 * In the case where the `$group_path_over_root_path` property is true, the order of priority will change to this:
	 *
	 * 1. If a path group is set, that will be used.
	 * 2. If a specific root path is set, that will be used.
	 * 3. Otherwise, the root path will be used.
	 *
	 * @var string
	 */
	protected string $group_path_name = '';

	/**
	 * Content or callable that should be printed after the asset.
	 *
	 * @var mixed
	 */
	protected $print_after;

	/**
	 * Content or callable that should be printed before the asset.
	 *
	 * @var mixed
	 */
	protected $print_before;

	/**
	 * The asset priority.
	 *
	 * @var int
	 */
	protected int $priority = 10;

	/**
	 * Whether the asset should print rather than enqueue.
	 *
	 * @var bool
	 */
	protected bool $should_print = false;

	/**
	 * Whether to use the asset directory prefix based on asset type.
	 *
	 * @var bool
	 */
	protected bool $should_use_asset_directory_prefix = true;

	/**
	 * The asset slug.
	 *
	 * @var ?string
	 */
	protected ?string $slug = null;

	/**
	 * The asset textdomain.
	 *
	 * @var string
	 */
	protected string $textdomain = '';

	/**
	 * Translation path.
	 *
	 * @var string
	 */
	protected string $translations_path = '';

	/**
	 * The asset type.
	 *
	 * @var ?string
	 */
	protected ?string $type = null;

	/**
	 * The asset file url.
	 *
	 * @var ?string
	 */
	protected ?string $url = null;

	/**
	 * Whether or not to attempt to load an .asset.php file.
	 *
	 * By default is true for scripts and false for styles.
	 *
	 * @since 1.3.1
	 *
	 * @var bool
	 */
	protected bool $use_asset_file = true;

	/**
	 * The asset version.
	 *
	 * @var ?string
	 */
	protected ?string $version = null;

	/**
	 * Whether to use the group path over the root path.
	 * This flag will be raised when the asset is added to a group path
	 * and lowered when it's removed from it.
	 *
	 * @since 1.4.3
	 *
	 * @var bool
	 */
	private $group_path_over_root_path = false;

	/**
	 * Constructor.
	 *
	 * @param string      $slug      The asset slug.
	 * @param string      $file      The asset file path.
	 * @param string|null $version   The asset version.
	 * @param string|null $root_path The path to the root of the plugin.
	 */
	public function __construct( string $slug, string $file, string $version = null, string $root_path = null ) {
		$this->slug      = sanitize_key( $slug );
		$this->file      = $file;
		$this->version   = $version ?? Config::get_version();
		$this->root_path = $root_path ?? Config::get_path();

		if (
			strpos( $this->file, 'vendor/' ) !== false
			|| strpos( $this->file, 'node_modules/' ) !== false
		) {
			$this->is_vendor = true;
		}

		$this->infer_type();
	}

	/**
	 * Adds the asset to a group path.
	 *
	 * @since 1.4.0
	 * @since 1.4.2 Also sets the usage of the Asset directory prefix based on the group path.
	 *
	 * @return static
	 */
	public function add_to_group_path( string $group_path_name ) {
		$this->group_path_name = $group_path_name;

		$this->prefix_asset_directory( Config::is_group_path_using_asset_directory_prefix( $this->group_path_name ) );

		$this->group_path_over_root_path = true;

		return $this;
	}

	/**
	 * Registers an asset.
	 *
	 * @param string      $slug      The asset slug.
	 * @param string      $file      The asset file path.
	 * @param string|null $version   The asset version.
	 * @param string|null $root_path The path to the root of the plugin.
	 */
	public static function add( string $slug, string $file, string $version = null, $root_path = null ) {
		return Assets::init()->add( new self( $slug, $file, $version, $root_path ) );
	}

	/**
	 * @since 1.0.0
	 *
	 * @param string $dependency
	 *
	 * @return static
	 */
	public function add_dependency( string $dependency ) {
		if ( isset( $this->dependencies[ $dependency ] ) ) {
			return $this;
		}

		$this->dependencies[ $dependency ] = $dependency;

		return $this;
	}

	/**
	 * Adds a wp_localize_script object to the asset.
	 *
	 * @since 1.0.0
	 *
	 * @param string $object_name JS object name.
	 * @param array|callable  $data Data assigned to the JS object. If a callable is passed, it will be called
	 *                              when the asset is enqueued and the return value will be used. The callable
	 *                              will be passed the asset as the first argument and should return an array.
	 *
	 * @return static
	 */
	public function add_localize_script( string $object_name, $data ) {
		if ( strpos( $object_name, '.' ) !== false ) {
			$this->custom_localize_script_objects[] = [ $object_name, $data ];
		} else {
			$this->wp_localize_script_objects[ $object_name ] = $data;
		}

		return $this;
	}

	/**
	 * Adds data to be attached to the stylesheet.
	 *
	 * @see   : wp_style_add_data()
	 *
	 * @since 1.0.0
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return static
	 */
	public function add_style_data( string $key, $value ) {
		$this->add_style_data[ $key ] = $value;
		return $this;
	}

	/**
	 * @since 1.0.0
	 *
	 * @param string $group
	 *
	 * @return static
	 */
	public function add_to_group( string $group ) {
		if ( isset( $this->groups[ $group ] ) ) {
			return $this;
		}

		$this->groups[ $group ] = $group;

		return $this;
	}

	/**
	 * Builds the path information for the asset.
	 *
	 * @return array<string,string>
	 */
	protected function build_resource_path_data(): array {
		$resource                = $this->get_file();
		$root_path               = $this->get_root_path();
		$relative_path_to_assets = $this->is_vendor() ? '' : null;

		if ( $root_path === null ) {
			$root_path = Config::get_path();
		}

		$hook_prefix   = Config::get_hook_prefix();
		$extension     = pathinfo( $resource, PATHINFO_EXTENSION );
		$resource_path = $relative_path_to_assets;
		$type          = $this->get_type();
		$prefix_dir    = '';

		if ( ! $extension && $type ) {
			$extension = $type;
		}

		$should_prefix = $this->should_use_asset_directory_prefix;

		if ( is_null( $resource_path ) ) {
			$resources_path = $this->get_path();
			$resource_path  = $resources_path;

			if ( $should_prefix ) {
				switch ( $extension ) {
					case 'css':
						$prefix_dir     = 'css';
						$resources_path = preg_replace( '#/css/$#', '/', $resources_path );
						$resource_path  = "{$resources_path}css/";
						break;
					case 'js':
						$prefix_dir     = 'js';
						$resources_path = preg_replace( '#/js/$#', '/', $resources_path );
						$resource_path  = "{$resources_path}js/";
						break;
					case 'scss':
						$prefix_dir     = 'scss';
						$resources_path = preg_replace( '#/scss/$#', '/', $resources_path );
						$resource_path  = "{$resources_path}scss/";
						break;
					case 'pcss':
						$prefix_dir     = 'postcss';
						$resources_path = preg_replace( '#/postcss/$#', '/', $resources_path );
						$resource_path  = "{$resources_path}postcss/";
						break;
					default:
						$resource_path = $resources_path;
						break;
				}

				if ( $prefix_dir && strpos( $resource, $prefix_dir . '/' ) === 0 ) {
					$resource = substr( $resource, strlen( $prefix_dir . '/' ) );
				}
			}
		}

		$data = [
			'resource_path' => $resource_path,
			'resource'      => $resource,
			'prefix_dir'    => $prefix_dir,
		];

		/**
		 * Filters the asset URL
		 *
		 * @param array<string,string> $data  Resource path data.
		 * @param string               $slug  Asset slug.
		 * @param Asset                $asset The Asset object.
		 */
		return (array) apply_filters( "stellarwp/assets/{$hook_prefix}/resource_path_data", $data, $this->get_slug(), $this );
	}

	/**
	 * Removes the asset from a group.
	 *
	 * This method is the inverse of the `add_to_group_path` method.
	 *
	 * @param string $group_path_name The name of the group path to remove the asset from.
	 *
	 * @return void The asset is removed from the specified group path.
	 */
	public function remove_from_group_path( string $group_path_name ): void {
		$this->group_path_over_root_path = false;
		$this->group_path_name           = '';
	}

	/**
	 * Builds the base asset URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function build_asset_url(): string {
		$resource_path_data = $this->build_resource_path_data();
		$resource           = $resource_path_data['resource'];
		$resource_path      = $resource_path_data['resource_path'];

		$root_path       = $this->get_root_path();
		$plugin_base_url = Config::get_url( $root_path );
		$hook_prefix     = Config::get_hook_prefix();

		$url = $plugin_base_url . $resource_path . $resource;

		/**
		 * Filters the asset URL
		 *
		 * @param string $url   Asset URL.
		 * @param string $slug  Asset slug.
		 * @param Asset  $asset The Asset object.
		 */
		return (string) apply_filters( "stellarwp/assets/{$hook_prefix}/resource_url", $url, $this->get_slug(), $this );
	}

	/**
	 * Builds the minified asset URL.
	 *
	 * @since 1.2.4
	 *
	 * @param string $original_url The original URL.
	 *
	 * @return string
	 */
	protected function build_min_asset_url( $original_url ): string {
		// debt: This is too much of a copy paste from build_asset_url. We should refactor this.
		$resource                = $this->get_file();
		$root_path               = $this->get_root_path();
		$relative_path_to_assets = $this->is_vendor() ? '' : null;

		if ( $root_path === null ) {
			$root_path = Config::get_path();
		}

		$plugin_base_url = Config::get_url( $root_path );
		$hook_prefix     = Config::get_hook_prefix();
		$extension       = pathinfo( $resource, PATHINFO_EXTENSION );
		$resource_path   = $relative_path_to_assets;
		$type            = $this->get_type();

		if ( ! $extension && $type ) {
			$extension = $type;
		}

		$should_prefix = $this->should_use_asset_directory_prefix;

		if ( is_null( $resource_path ) ) {
			$resources_path = $this->get_path();
			$resource_path  = $resources_path;

			if ( $should_prefix ) {
				$prefix_dir = '';

				switch ( $extension ) {
					case 'css':
						$prefix_dir     = 'css';
						$resources_path = preg_replace( '#/css/$#', '/', $resources_path );
						$resource_path  = "{$resources_path}css/";
						break;
					case 'js':
						$prefix_dir     = 'js';
						$resources_path = preg_replace( '#/js/$#', '/', $resources_path );
						$resource_path  = "{$resources_path}js/";
						break;
					case 'scss':
						$prefix_dir     = 'scss';
						$resources_path = preg_replace( '#/scss/$#', '/', $resources_path );
						$resource_path  = "{$resources_path}scss/";
						break;
					case 'pcss':
						$prefix_dir     = 'postcss';
						$resources_path = preg_replace( '#/postcss/$#', '/', $resources_path );
						$resource_path  = "{$resources_path}postcss/";
						break;
					default:
						$resource_path = $resources_path;
						break;
				}

				if ( $prefix_dir && strpos( $resource, $prefix_dir . '/' ) === 0 ) {
					$resource = substr( $resource, strlen( $prefix_dir . '/' ) );
				}
			}
		}

		$relative_asset_path = $this->get_path();
		$min_asset_path      = $this->get_min_path();

		if ( $min_asset_path !== $relative_asset_path ) {
			$minified_file_path = preg_replace( '#(.*)(' . preg_quote( $relative_asset_path, '#' ) . ')(.*[a-zA-Z0-0\-\_\.]+).(js|css)#', '$1' . $min_asset_path . '$3.min.$4', $resource_path . $resource );
		} else {
			$minified_file_path = preg_replace( '#(.*).(js|css)#', '$1.min.$2', $resource_path . $resource );
		}

		$script_debug = defined( 'SCRIPT_DEBUG' ) && Utils::is_truthy( SCRIPT_DEBUG );

		if ( $script_debug && is_file( wp_normalize_path( $root_path . $resource_path . $resource ) ) ) {
			return $original_url;
		}

		$minified_abs_file_path = wp_normalize_path( $root_path . $minified_file_path );

		if ( ! is_file( $minified_abs_file_path ) ) {
			return $original_url;
		}

		$url = $plugin_base_url . $minified_file_path;

		/**
		 * Filters the min asset URL
		 *
		 * @param string $url   Asset URL.
		 * @param string $slug  Asset slug.
		 * @param Asset  $asset The Asset object.
		 */
		return (string) apply_filters( "stellarwp/assets/{$hook_prefix}/min_resource_url", $url, $this->get_slug(), $this );
	}

	/**
	 * Set a callable that should fire after enqueuing.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $callable A callable that executes after the asset is enqueued.
	 *
	 * @return static
	 */
	public function call_after_enqueue( $callable ) {
		if ( ! is_callable( $callable ) ) {
			throw new InvalidArgumentException( 'The argument must be a callable.' );
		}

		$this->after_enqueue = $callable;
		return $this;
	}

	/**
	 * Clone the asset to another type (JS to CSS or vice versa).
	 *
	 * This assumes that both the CSS and JS assets are in the same directory. If
	 * more differentiation is needed, modify the clone or create the asset separately.
	 *
	 * @since 1.3.1
	 *
	 * @param string          $clone_type      The type of asset to register- 'css' or 'js'.
	 * @param string|callable ...$dependencies The dependencies to add to the cloned asset.
	 *
	 * @return self
	 */
	public function clone_to( string $clone_type, ...$dependencies ) {
		$source_type = $this->get_type();

		if ( $clone_type === $source_type ) {
			throw new \InvalidArgumentException( 'The clone type must be different from the source type.' );
		}

		if ( ! in_array( $clone_type, [ 'css', 'js' ], true ) ) {
			throw new \InvalidArgumentException( 'The clone type must be either "css" or "js".' );
		}

		$slug  = $this->slug;
		$slug  = preg_replace( "/-(css|js|script|style)$/", '', $slug );
		$slug .= "-{$clone_type}";

		$clone = static::add(
			$slug,
			str_replace( ".{$source_type}", ".{$clone_type}", $this->file ),
			$this->version,
			$this->get_root_path()
		);

		$condition  = $this->get_condition();
		$enqueue_on = $this->get_enqueue_on();
		$groups     = $this->get_groups();
		$priority   = $this->get_priority();
		$path       = $this->get_path();
		$min_path   = $this->get_min_path();

		$clone->use_asset_file( false );
		$clone->prefix_asset_directory( $this->should_use_asset_directory_prefix );

		if ( $dependencies ) {
			foreach ( $dependencies as $dependency ) {
				$clone->add_dependency( $dependency );
			}
		}

		if ( $condition ) {
			$clone->set_condition( $condition );
		}

		if ( $enqueue_on ) {
			foreach ( $enqueue_on as $on ) {
				$clone->enqueue_on(
					$on,
					$priority
				);
			}
		}

		if ( $groups ) {
			foreach ( $groups as $group ) {
				$clone->add_to_group( $group );
			}
		}

		if ( $path ) {
			$clone->set_path( $path );
		}

		if ( $min_path ) {
			$clone->set_min_path( $min_path );
		}

		return $clone;
	}

	/**
	 * Performs the actual enqueueing of the asset.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $should_force Whether to force the enqueueing and ignore any conditionals.
	 *
	 * @return void
	 */
	public function enqueue( bool $should_force = false ) {
		Assets::init()->enqueue( $this->get_slug(), $should_force );
	}

	/**
	 * Enqueue the asset on an action.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action WordPress action that this asset will be registered to.
	 *
	 * @return static
	 */
	public function enqueue_on( string $action, $priority = null ) {
		if ( ! is_null( $priority ) ) {
			$this->set_priority( $priority );
		}

		$this->action[ $action ] = $action;

		return $this;
	}

	/**
	 * Get the asset action.
	 *
	 * @return array
	 */
	public function get_action(): array {
		return $this->action;
	}

	/**
	 * Get the asset after enqueue callable.
	 *
	 * @return mixed
	 */
	public function get_after_enqueue() {
		return $this->after_enqueue;
	}

	/**
	 * Get the asset asset file contents.
	 *
	 * @return array
	 */
	public function get_asset_file_contents(): array {
		if ( ! empty( $this->asset_file_contents ) ) {
			return $this->asset_file_contents;
		}

		$default = [
			'dependencies' => [],
			'version'      => null,
		];

		if ( ! $this->has_asset_file() ) {
			$this->asset_file_contents = $default;

			return $this->asset_file_contents;
		}

		$asset_file_contents = include $this->get_asset_file_path();

		if ( ! is_array( $asset_file_contents ) ) {
			$this->asset_file_contents = $default;

			return $this->asset_file_contents;
		}

		$asset_file_contents                 = wp_parse_args( $asset_file_contents, $default );
		$asset_file_contents['dependencies'] = array_unique( $asset_file_contents['dependencies'] );

		$this->asset_file_contents = $asset_file_contents;

		return $this->asset_file_contents;
	}

	/**
	 * Get the asset asset file path.
	 *
	 * @return string
	 */
	public function get_asset_file_path(): string {
		if ( $this->asset_file_path === '' ) {
			$resource_path_data    = $this->build_resource_path_data();
			$this->asset_file_path = $this->get_root_path() . $resource_path_data['resource_path'] . str_replace( [ '.css', '.js' ], '', $this->get_file() ) . '.asset.php';
		}

		return $this->asset_file_path;
	}

	/**
	 * Get the asset condition callable.
	 *
	 * @return mixed
	 */
	public function get_condition() {
		return $this->condition;
	}

	/**
	 * Get the asset dependencies.
	 *
	 * @return array<string>
	 */
	public function get_dependencies(): array {
		$dependencies = $this->dependencies;

		if ( is_callable( $dependencies ) ) {
			$dependencies = $dependencies( $this );
		}

		$asset_file_contents = $this->get_asset_file_contents();

		if ( ! empty( $asset_file_contents['dependencies'] ) ) {
			$dependencies = array_unique(
				array_merge(
					$asset_file_contents['dependencies'],
					$dependencies
				)
			);
		}

		return (array) $dependencies;
	}

	/**
	 * Get the asset's enqueue action.
	 *
	 * @return array
	 */
	public function get_enqueue_on(): array {
		return $this->action;
	}

	/**
	 * Get the asset file.
	 *
	 * @return string
	 */
	public function get_file(): string {
		return $this->file;
	}

	/**
	 * Get the asset groups.
	 *
	 * @return array
	 */
	public function get_groups(): array {
		return $this->groups;
	}

	/**
	 * Get the asset wp_localize_script_objects.
	 *
	 * @return array
	 */
	public function get_localize_scripts(): array {
		return $this->wp_localize_script_objects;
	}

	/**
	 * Get the asset wp_localize_script_objects.
	 *
	 * @return array<array{0: string, 1: mixed}> A set of data to localized using dot-notation.
	 */
	public function get_custom_localize_scripts(): array {
		return $this->custom_localize_script_objects;
	}

	/**
	 * Get the asset media setting.
	 *
	 * @return string
	 */
	public function get_media(): string {
		return $this->media;
	}

	/**
	 * Get the asset min path.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_min_path(): string {
		if ( $this->min_path === null ) {
			return $this->get_path();
		}

		return $this->min_path;
	}

	/**
	 * Get the asset min url.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_min_url(): string {
		$this->get_url();

		if ( $this->min_url === null ) {
			return $this->url;
		}

		return $this->min_url;
	}

	/**
	 * Get the asset min path.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_path(): string {
		if ( $this->path === null ) {
			$group_relative = $this->group_path_name ? Config::get_relative_path_of_group_path( $this->group_path_name ) : '';
			return $group_relative ? $group_relative : Config::get_relative_asset_path();
		}

		return $this->path;
	}

	/**
	 * Gets the root path for the resource.
	 *
	 * @return ?string
	 */
	public function get_root_path(): ?string {
		if ( ! $this->group_path_name ) {
			return $this->root_path;
		}

		if ( $this->group_path_over_root_path ) {
			$group_path = Config::get_path_of_group_path( $this->group_path_name );

			return $group_path ?: $this->root_path;
		}

		if ( $this->root_path !== Config::get_path() ) {
			return $this->root_path;
		}

		$group_path = Config::get_path_of_group_path( $this->group_path_name );

		return $group_path ?: $this->root_path;
	}

	/**
	 * Get the print_after value.
	 *
	 * @return mixed
	 */
	public function get_print_after() {
		return $this->print_after;
	}

	/**
	 * Get the print_before value.
	 *
	 * @return mixed
	 */
	public function get_print_before() {
		return $this->print_before;
	}

	/**
	 * Get the asset action priority.
	 *
	 * @return int
	 */
	public function get_priority(): int {
		return $this->priority;
	}

	/**
	 * Get the asset slug.
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Get the asset textdomain.
	 *
	 * @return string
	 */
	public function get_textdomain(): string {
		return $this->textdomain;
	}

	/**
	 * Get the asset translation path.
	 *
	 * @since 1.3.1
	 *
	 * @return string
	 */
	public function get_translation_path(): string {
		return Config::get_path() . $this->translations_path;
	}

	/**
	 * Get the asset style data.
	 *
	 * @return array
	 */
	public function get_style_data(): array {
		return $this->add_style_data;
	}

	/**
	 * Get the asset type.
	 *
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Get the asset url.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $use_min_if_available Use the minified version of the asset if available.
	 *
	 * @return string
	 */
	public function get_url( bool $use_min_if_available = true ): string {
		if ( $this->url === null ) {
			if ( filter_var( $this->file, FILTER_VALIDATE_URL ) ) {
				$this->url = $this->file;
			} else {
				$this->url = $this->build_asset_url();
			}
		}

		if ( $this->min_url === null ) {
			$this->min_url = $this->build_min_asset_url( $this->url );
		}

		if ( $use_min_if_available && $this->min_url ) {
			return $this->min_url;
		}

		return $this->url;
	}

	/**
	 * Get the asset's full path - considering if minified exists.
	 *
	 * @since 1.4.6
	 * @since 1.4.7 When the path is a URL, return the URL.
	 *
	 * @param bool $use_min_if_available
	 *
	 * @return string
	 */
	public function get_full_resource_path( bool $use_min_if_available = true ): string {
		$resource_path_data = $this->build_resource_path_data();
		if ( empty( $resource_path_data['resource'] ) ) {
			return '';
		}

		if (
			str_starts_with( $resource_path_data['resource'], 'http://' ) ||
			str_starts_with( $resource_path_data['resource'], 'https://' ) ||
			str_starts_with( $resource_path_data['resource'], '//' )
		) {
			return $resource_path_data['resource'];
		}
		$resource           = $resource_path_data['resource'];
		$resource_path      = $resource_path_data['resource_path'];

		$root_path       = $this->get_root_path();

		$path = wp_normalize_path( $root_path . $resource_path . $resource );

		if ( ! $use_min_if_available ) {
			return $path;
		}

		if ( strstr( $path, '.min.' . $this->get_type() ) ) {
			return $path;
		}

		$min_relative_path = $this->get_min_path();
		$min_path = $min_relative_path === $this->get_path() ? preg_replace( '#(.*).(js|css)#', '$1.min.$2', $path ) : $root_path . $min_relative_path . $resource;
		$min_path = wp_normalize_path( $min_path );

		return file_exists( $min_path ) ? $min_path : $path;
	}

	/**
	 * Get the asset version.
	 *
	 * @return string
	 */
	public function get_version(): string {
		$asset_file_contents = $this->get_asset_file_contents();

		if ( ! empty( $asset_file_contents['version'] ) ) {
			return (string) $asset_file_contents['version'];
		}

		$hook_prefix = Config::get_hook_prefix();

		/**
		 * Filters the asset version when it doesn't come from an asset file.
		 *
		 * @param string $version The asset version.
		 * @param string $slug    The asset slug.
		 * @param Asset  $asset   The Asset object.
		 */
		return (string) apply_filters( "stellarwp/assets/{$hook_prefix}/version", $this->version, $this->slug, $this );
	}

	/**
	 * Determines if the asset has an asset.php file.
	 *
	 * @return boolean
	 */
	public function has_asset_file(): bool {
		if ( ! $this->use_asset_file ) {
			return false;
		}

		$asset_file_path = $this->get_asset_file_path();

		if ( empty( $asset_file_path ) ) {
			return false;
		}

		return is_file( $asset_file_path );
	}

	/**
	 * Sets the asset to be loaded in the footer.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public function in_footer() {
		$this->in_footer = true;

		return $this;
	}

	/**
	 * Sets the asset to be loaded in the header.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public function in_header() {
		$this->in_footer = false;

		return $this;
	}

	/**
	 * Set the asset type.
	 *
	 * @since 1.0.0
	 * @since 1.4.4 - For css files, we dont want to use asset file for dependencies by default.
	 */
	protected function infer_type() {
		if ( substr( $this->file, -3, 3 ) === '.js' ) {
			$this->type = 'js';
		} elseif ( substr( $this->file, -4, 4 ) === '.css' ) {
			$this->type = 'css';
			$this->use_asset_file( false );
		}
	}

	/**
	 * Returns whether or not the asset is async.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_async(): bool {
		return $this->is_async;
	}

	/**
	 * Returns whether or not the asset is a CSS asset.
	 *
	 * @return boolean
	 */
	public function is_css(): bool {
		return $this->get_type() === 'css';
	}

	/**
	 * Returns whether or not the asset is deferred.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_deferred(): bool {
		return $this->is_deferred;
	}

	/**
	 * Returns whether or not the asset is enqueued.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_enqueued(): bool {
		return $this->is_enqueued;
	}

	/**
	 * Returns whether or not the asset goes in the footer.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_in_footer(): bool {
		return $this->in_footer;
	}

	/**
	 * Returns whether or not the asset goes in the header.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_in_header(): bool {
		return ! $this->in_footer;
	}

	/**
	 * Returns whether or not the asset is a JS asset.
	 *
	 * @return boolean
	 */
	public function is_js(): bool {
		return $this->get_type() === 'js';
	}

	/**
	 * Returns whether or not the asset is a module.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_module(): bool {
		return $this->is_module;
	}

	/**
	 * Returns whether or not the asset is printed.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_printed(): bool {
		return $this->is_printed;
	}

	/**
	 * Returns whether or not the asset is registered.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_registered(): bool {
		return $this->is_registered;
	}

	/**
	 * Returns whether or not the asset is a vendor asset.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_vendor(): bool {
		return $this->is_vendor;
	}

	/**
	 * Returns the path to a minified version of a js or css file, if it exists.
	 * If the file does not exist, returns false.
	 *
	 * @since 1.0.0
	 *
	 * @deprecated 1.2.4 Use build_min_asset_url() instead.
	 *
	 * @param string $url The absolute URL to the un-minified file.
	 *
	 * @return string|false The url to the minified version or false, if file not found.
	 */
	public function maybe_get_min_file( $url ) {
		_deprecated_function( __METHOD__, '1.2.4', __CLASS__ . '::build_min_asset_url()' );
		$bases = Utils::get_bases();

		$urls = [];

		$wpmu_plugin_url = $bases['wpmu_plugin']['base_url'];
		$wp_plugin_url   = $bases['wp_plugin']['base_url'];
		$wp_content_url  = $bases['wp_content']['base_url'];
		$plugins_url     = $bases['plugins']['base_url'];
		$stylesheet_url  = $bases['stylesheet']['base_url'];

		if ( 0 === strpos( $url, $wpmu_plugin_url ) ) {
			// URL inside WPMU plugin dir.
			$base_dir = $bases['wpmu_plugin']['base_dir'];
			$base_url = $bases['wpmu_plugin']['base_url'];
		} elseif ( 0 === strpos( $url, $wp_plugin_url ) ) {
			// URL inside WP plugin dir.
			$base_dir = $bases['wp_plugin']['base_dir'];
			$base_url = $bases['wp_plugin']['base_url'];
		} elseif ( 0 === strpos( $url, $wp_content_url ) ) {
			// URL inside WP content dir.
			$base_dir = $bases['wp_content']['base_dir'];
			$base_url = $bases['wp_content']['base_url'];
		} elseif ( 0 === strpos( $url, $plugins_url ) ) {
			$base_dir = $bases['plugins']['base_dir'];
			$base_url = $bases['plugins']['base_url'];
		} elseif ( 0 === strpos( $url, $stylesheet_url ) ) {
			$base_dir = $bases['stylesheet']['base_dir'];
			$base_url = $bases['stylesheet']['base_url'];
		} else {
			// Resource needs to be inside wp-content or a plugins dir.
			return false;
		}

		$script_debug = defined( 'SCRIPT_DEBUG' ) && Utils::is_truthy( SCRIPT_DEBUG );

		// Strip the plugin URL and make this relative.
		$relative_location = str_replace( $base_url . '/', '', $url );

		if ( $script_debug ) {
			// Add the actual url after having the min file added.
			$urls[] = $relative_location;
		}

		$relative_asset_path = $this->get_path();
		$min_asset_path      = $this->get_min_path();

		// If needed add the Min Files.
		if (
			substr( $relative_location, -3, 3 ) === '.js'
			|| substr( $relative_location, -4, 4 ) === '.css'
		) {
			if ( $min_asset_path !== $relative_asset_path ) {
				$urls[] = preg_replace( '#(.*)(' . preg_quote( $relative_asset_path, '#' ) . ')(.*[a-zA-Z0-0\-\_\.]+).(js|css)#', '$1' . $min_asset_path . '$3.min.$4', $relative_location );
			} else {
				$urls[] = preg_replace( '#(.*).(js|css)#', '$1.min.$2', $relative_location );
			}
		}

		if ( ! $script_debug ) {
			// Add the actual url after having the min file added.
			$urls[] = $relative_location;
		}

		// Check for all Urls added to the array.
		foreach ( $urls as $partial_path ) {
			$file_path = wp_normalize_path( "{$base_dir}/{$partial_path}" );
			$file_url  = "{$base_url}/{$partial_path}";

			if ( is_file( $file_path ) ) {
				return $file_url;
			}
		}

		// If we don't have any real file return false.
		return false;
	}

	/**
	 * Sets whether or not to use the asset directory prefix (css/ or js/).
	 *
	 * @since 1.3.1
	 *
	 * @param boolean $prefix_asset_directory Whether to use the asset directory prefix.
	 * @return static
	 */
	public function prefix_asset_directory( bool $prefix_asset_directory = true ): self {
		$this->should_use_asset_directory_prefix = $prefix_asset_directory;
		return $this;
	}

	/**
	 * Print the asset.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public function print() {
		$this->should_print = true;
		return $this;
	}

	/**
	 * Set the print_after value.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $thing A string or callable to print after the asset.
	 *
	 * @return static
	 */
	public function print_after( $thing ) {
		$this->print_after = $thing;

		return $this;
	}

	/**
	 * Set the print_before value.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $thing A string or callable to print before the asset.
	 *
	 * @return static
	 */
	public function print_before( $thing ) {
		$this->print_before = $thing;

		return $this;
	}

	/**
	 * Enqueue the asset.
	 *
	 * @since 1.0.0
	 * @since 1.3.1 Returns itself to enable chaining.
	 *
	 * @return static
	 */
	public function register(): self {
		Assets::init()->register_in_wp( $this );

		return $this;
	}

	/**
	 * Register the asset along with registering a CSS asset from the same directory.
	 *
	 * @since 1.3.1
	 *
	 * @param string|callable ...$dependencies The dependencies to add to the cloned asset.
	 *
	 * @return static
	 */
	public function register_with_css( ...$dependencies ): self {
		$this->prefix_asset_directory( false );
		$this->register();
		$asset = $this->clone_to( 'css', ...$dependencies );
		$asset->register();

		return $this;
	}

	/**
	 * Register the asset along with registering a JS asset from the same directory.
	 *
	 * @since 1.3.1
	 *
	 * @param string|callable ...$dependencies The dependencies to add to the cloned asset.
	 *
	 * @return static
	 */
	public function register_with_js( ...$dependencies ): self {
		$this->prefix_asset_directory( false );
		$this->register();
		$asset = $this->clone_to( 'js', ...$dependencies );
		$asset->register();

		return $this;
	}

	/**
	 * @since 1.0.0
	 *
	 * @param string $group
	 *
	 * @return static
	 */
	public function remove_from_group( string $group ) {
		if ( ! isset( $this->groups[ $group ] ) ) {
			return $this;
		}

		unset( $this->groups[ $group ] );

		return $this;
	}

	/**
	 * Set the asset action.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action WordPress action that this asset will be registered to.
	 *
	 * @return static
	 */
	public function set_action( string $action ) {
		$this->action[ $action ] = $action;

		return $this;
	}

	/**
	 * Set the asset file path for the asset.
	 *
	 * @since 1.3.0
	 *
	 * @param string $path The partial path to the asset.
	 *
	 * @return static
	 */
	public function set_asset_file( string $path ) {
		if ( strpos( $path, '.asset.php' ) === false ) {
			$path = preg_replace( '/\.(js|css)$/', '', $path );
			$path .= '.asset.php';
		}

		$this->asset_file_path = $this->get_root_path() . $this->get_path() . $path;

		// Since we are setting a new asset file path, reset the asset file contents.
		$this->asset_file_contents = [];

		return $this;
	}

	/**
	 * Set the asset as async.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public function set_as_async() {
		$this->is_async = true;
		return $this;
	}

	/**
	 * Set the asset as deferred.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public function set_as_deferred() {
		$this->is_deferred = true;
		return $this;
	}

	/**
	 * Set the asset enqueue status to true.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public function set_as_enqueued() {
		$this->is_enqueued = true;
		return $this;
	}

	/**
	 * Set the directory where asset should be retrieved.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $path                                                 The path to the minified file.
	 * @param bool|null   $should_automatically_use_asset_type_directory_prefix Whether to prefix files automatically by type (e.g. js/ for JS). Defaults to true.
	 *
	 * @return $this
	 */
	public function set_path( ?string $path = null, $should_automatically_use_asset_type_directory_prefix = null ) {
		$this->path = trailingslashit( $path );

		if ( $should_automatically_use_asset_type_directory_prefix !== null ) {
			$this->prefix_asset_directory( $should_automatically_use_asset_type_directory_prefix );
		}

		return $this;
	}

	/**
	 * Set the directory where min files should be retrieved.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $path The path to the minified file.
	 *
	 * @return $this
	 */
	public function set_min_path( ?string $path = null ) {
		$this->min_path = trailingslashit( $path );
		return $this;
	}

	/**
	 * Set the asset as a module.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public function set_as_module() {
		$this->is_module = true;
		return $this;
	}

	/**
	 * Set the asset as not async.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public function set_as_not_async() {
		$this->is_async = false;
		return $this;
	}

	/**
	 * Set the asset as not deferred.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public function set_as_not_deferred() {
		$this->is_deferred = false;
		return $this;
	}

	/**
	 * Set the asset print status to false.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public function set_as_not_printed() {
		$this->is_printed = false;
		return $this;
	}

	/**
	 * Set the asset print status to true.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public function set_as_printed() {
		$this->is_printed = true;
		return $this;
	}

	/**
	 * Set the asset registration status to true.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public function set_as_registered() {
		$this->is_registered = true;
		return $this;
	}

	/**
	 * Set the asset enqueue status to false.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public function set_as_unenqueued() {
		$this->is_enqueued = false;
		return $this;
	}

	/**
	 * Set the asset registration status to false.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public function set_as_unregistered() {
		$this->is_registered = false;
		return $this;
	}

	/**
	 * Set the asset condition for inclusion.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $condition A callable that returns a boolean indicating if the asset should be enqueued.
	 *
	 * @return static
	 */
	public function set_condition( $condition ) {
		if ( ! is_callable( $condition ) ) {
			throw new InvalidArgumentException( 'The condition argument must be a callable.' );
		}

		$this->condition = $condition;
		return $this;
	}

	/**
	 * @since 1.0.0
	 *
	 * @param string|callable ...$dependencies
	 *
	 * @return static
	 */
	public function set_dependencies( ...$dependencies ) {
		if ( $dependencies[0] && is_callable( $dependencies[0] ) ) {
			$this->dependencies = $dependencies[0];
		} else {
			$this->dependencies = (array) $dependencies;
		}

		return $this;
	}

	/**
	 * Set the asset media.
	 *
	 * @since 1.0.0
	 *
	 * @param string $media Asset media setting.
	 *
	 * @return static
	 */
	public function set_media( string $media ) {
		$this->media = esc_attr( $media );
		return $this;
	}

	/**
	 * Set the asset priority.
	 *
	 * @since 1.0.0
	 *
	 * @param int $priority Asset priority.
	 *
	 * @return static
	 */
	public function set_priority( int $priority ) {
		$this->priority = absint( $priority );
		return $this;
	}

	/**
	 * Set the translation path. Alias of with_translations().
	 *
	 * @since 1.3.1
	 *
	 * @param string $textdomain The textdomain of the asset.
	 * @param string $path Relative path to the translations directory.
	 * @return self
	 */
	public function set_translations( string $textdomain, string $path ): self {
		return $this->with_translations( $textdomain, $path );
	}

	/**
	 * Set the asset type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Asset type.
	 *
	 * @return static
	 */
	public function set_type( string $type ) {
		$this->type = $type;
		return $this;
	}

	/**
	 * Get whether or not the script should print.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function should_print(): bool {
		return $this->should_print;
	}

	/**
	 * Set whether or not to use an .asset.php file.
	 *
	 * @since 1.3.1
	 *
	 * @param boolean $use_asset_file Whether to use an .asset.php file.
	 * @return self
	 */
	public function use_asset_file( bool $use_asset_file = true ): self {
		$this->use_asset_file = $use_asset_file;
		return $this;
	}

	/**
	 * Set the translation path.
	 *
	 * @since 1.3.1
	 *
	 * @param string $textdomain The textdomain of the asset.
	 * @param string $path Relative path to the translations directory.
	 *
	 * @throws InvalidArgumentException If the asset is not a JS asset.
	 *
	 * @return self
	 */
	public function with_translations( string $textdomain = 'default', string $path = 'languages' ): self {
		if ( ! $this->is_js() ) {
			throw new InvalidArgumentException( 'Translations may only be set for JS assets.' );
		}

		$this->translations_path = $path;
		$this->textdomain        = $textdomain;
		return $this;
	}
}
