<?php
namespace AIOSEO\TableOfContents\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the loading of our file assets.
 *
 * @since 1.0.0
 */
class Assets {
	/**
	 * Whether we should load dev scripts.
	 *
	 * @since 1.0.0
	 *
	 * @var boolean|null
	 */
	private $shouldLoadDevScripts = null;

	/**
	 * The script handle to use for asset enqueuing.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $scriptHandle = 'table-of-contents-plus';

	/**
	 * Holds the location of the manifest file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $manifestFile = '';

	/**
	 * True if we are in a dev environment. This mirrors the global isDev.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $isDev = false;

	/**
	 * Asset handles that should load as regular JS and not as modern JS module.
	 *
	 * @since 1.0.0
	 *
	 * @var array An array of handles.
	 */
	private $noModuleTag = [];

	/**
	 * Core class instance.
	 *
	 * @since 1.1.0
	 *
	 * @var Core
	 */
	public $core = null;

	/**
	 * The plugin version.
	 *
	 * @since 1.1.0
	 *
	 * @var int
	 */
	public $version = 0;

	/**
	 * The domain.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public $domain = '';

	/**
	 * The port.
	 *
	 * @since 1.1.0
	 *
	 * @var int
	 */
	public $port = 0;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Core $core The AIOSEO Core class.
	 */
	public function __construct( $core ) {
		$this->core         = $core;
		$this->version      = aioseoTableOfContents()->version;
		$this->manifestFile = AIOSEO_TABLE_OF_CONTENTS_DIR . '/dist/manifest.php';
		$this->isDev        = aioseoTableOfContents()->isDev;

		if ( $this->isDev ) {
			$this->domain = getenv( 'VITE_AIOSEO_TABLE_OF_CONTENTS_DOMAIN' );
			$this->port   = getenv( 'VITE_AIOSEO_TABLE_OF_CONTENTS_DEV_PORT' );
		}

		add_filter( 'script_loader_tag', [ $this, 'scriptLoaderTag' ], 10, 3 );
	}

	/**
	 * The asset to load.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset        The asset to load.
	 * @param  array  $dependencies An array of dependencies.
	 * @param  mixed  $data         Any data to be localized.
	 * @param  string $objectName   The object name to use when localizing.
	 * @return void
	 */
	public function load( $asset, $dependencies = [], $data = null, $objectName = 'aioseoTableOfContents' ) {
		$this->jsPreloadImports( $asset );
		$this->loadCss( $asset );
		$this->enqueueJs( $asset, $dependencies, $data, $objectName );
	}

	/**
	 * Filter the script loader tag if this is our script.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $tag    The tag that is going to be output.
	 * @param  string $handle The handle for the script.
	 * @return string         The modified tag.
	 */
	public function scriptLoaderTag( $tag, $handle, $src ) {
		if ( $this->skipModuleTag( $handle ) ) {
			return $tag;
		}

		$tag = str_replace( $src, $this->normalizeAssetsHost( $src ), $tag );

		// Remove the type and re-add it as module.
		$tag = preg_replace( '/type=[\'"].*?[\'"]/', '', (string) $tag );
		$tag = preg_replace( '/<script/', '<script type="module"', (string) $tag );

		return $tag;
	}

	/**
	 * Preload JS imports.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to load imports for.
	 * @return void
	 */
	private function jsPreloadImports( $asset ) {
		static $urls = []; // Prevent script from being loaded multiple times.

		$res = '';
		foreach ( $this->importsUrls( $asset ) as $url ) {
			if ( isset( $urls[ $url ] ) ) {
				continue;
			}

			$urls[ $url ] = true;

			$res .= '<link rel="modulepreload" href="' . esc_attr( $url ) . "\">\n";
		}

		$allowedHtml = [
			'link' => [
				'rel'  => [],
				'href' => []
			]
		];

		if ( ! empty( $res ) ) {
			if ( ! function_exists( 'wp_enqueue_script_module' ) ) {
				add_action( 'admin_head', function () use ( &$res, $allowedHtml ) {
					echo wp_kses( $res, $allowedHtml );
				} );
				add_action( 'wp_head', function () use ( &$res, $allowedHtml ) {
					echo wp_kses( $res, $allowedHtml );
				} );
			} else {
				add_action( 'admin_print_footer_scripts', function () use ( &$res, $allowedHtml ) {
					echo wp_kses( $res, $allowedHtml );
				}, 1000 );
			}
		}
	}

	/**
	 * Loads CSS for an asset from the manifest file.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The script to load CSS for.
	 * @return void
	 */
	private function loadCss( $asset ) {
		if ( $this->shouldLoadDev() ) {
			return;
		}

		foreach ( $this->getCssUrls( $asset ) as $file => $url ) {
			wp_enqueue_style( $this->cssHandle( $file ), $url, [], $this->version );
		}
	}

	/**
	 * Register a CSS asset.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset        The script to load CSS for.
	 * @param  array  $dependencies An array of dependencies.
	 * @param  string $devPath      The file's dev path.
	 * @return void
	 */
	public function registerCss( $asset, $dependencies = [], $devPath = '' ) {
		$handle = $this->cssHandle( $asset );
		if ( wp_style_is( $handle, 'registered' ) ) {
			return;
		}

		$devPath = $devPath ?: $asset;

		$url = $this->shouldLoadDev()
			? $this->getDevUrl() . ltrim( $devPath, '/' )
			: $this->assetUrl( $asset );

		if ( ! $url ) {
			return;
		}

		wp_register_style( $handle, $url, $dependencies, $this->version );
	}

	/**
	 * Enqueue css.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset        The css to load.
	 * @param  string $devPath      The file's dev path.
	 * @param  array  $dependencies An array of dependencies.
	 * @return void
	 */
	public function enqueueCss( $asset, $dependencies = [], $devPath = '' ) {
		$this->registerCss( $asset, $dependencies, $devPath );

		$handle = $this->cssHandle( $asset );
		if ( wp_style_is( $handle, 'enqueued' ) ) {
			return;
		}

		wp_enqueue_style( $handle );
	}

	/**
	 * Register the JS to enqueue.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset        The script to load.
	 * @param  array  $dependencies An array of dependencies.
	 * @param  mixed  $data         Any data to be localized.
	 * @param  string $objectName   The object name to use when localizing.
	 * @return void
	 */
	public function registerJs( $asset, $dependencies = [], $data = null, $objectName = 'aioseoTableOfContents' ) {
		$handle = $this->jsHandle( $asset );
		if ( wp_script_is( $handle, 'registered' ) ) {
			return;
		}

		$url = $this->shouldLoadDev()
			? $this->getDevUrl() . ltrim( $asset, '/' )
			: $this->jsUrl( $asset );

		if ( ! $url ) {
			return;
		}

		wp_register_script( $handle, $url, $dependencies, $this->version, true );

		if ( empty( $data ) ) {
			return;
		}

		wp_localize_script(
			$handle,
			$objectName,
			$data
		);
	}

	/**
	 * Register the JS to enqueue.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset        The script to load.
	 * @param  array  $dependencies An array of dependencies.
	 * @param  mixed  $data         Any data to be localized.
	 * @param  string $objectName   The object name to use when localizing.
	 * @return void
	 */
	public function enqueueJs( $asset, $dependencies = [], $data = null, $objectName = 'aioseoTableOfContents' ) {
		$this->registerJs( $asset, $dependencies, $data, $objectName );

		$handle = $this->jsHandle( $asset );
		if ( wp_script_is( $handle, 'enqueued' ) ) {
			return;
		}

		wp_enqueue_script( $handle );
	}

	/**
	 * Return the dev URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string The dev URL.
	 */
	private function getDevUrl() {
		$protocol = is_ssl() ? 'https://' : 'http://';

		return $protocol . $this->domain . ':' . $this->port . '/';
	}

	/**
	 * Get the asset URL.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find the URL for.
	 * @return string        The URL for the asset.
	 */
	private function assetUrl( $asset ) {
		$assetManifest = $this->getAssetManifestItem( $asset );

		return ! empty( $assetManifest['file'] )
			? $this->basePath() . $assetManifest['file']
			: $this->basePath() . ltrim( $asset, '/' );
	}

	/**
	 * Get the JS URL.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find the URL for.
	 * @return string        The URL for the asset.
	 */
	public function jsUrl( $asset ) {
		$manifestAsset = $this->getManifestItem( $asset );

		return ! empty( $manifestAsset['file'] )
			? $this->basePath() . $manifestAsset['file']
			: $this->basePath() . ltrim( $asset, '/' );
	}

	/**
	 * Get an item from the manifest.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find.
	 * @return string        Manifest object.
	 */
	private function getManifestItem( $asset ) {
		$manifest = $this->getManifest();

		$asset = ltrim( $asset, '/' );

		return isset( $manifest[ $asset ] ) ? $manifest[ $asset ] : null;
	}

	/**
	 * Get the CSS asset handle.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find the handle for.
	 * @return string        The asset handle.
	 */
	public function cssHandle( $asset ) {
		return "{$this->scriptHandle}/css/$asset";
	}

	/**
	 * Get the JS asset handle.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find the handle for.
	 * @return string        The asset handle.
	 */
	public function jsHandle( $asset = '' ) {
		return "{$this->scriptHandle}/js/$asset";
	}

	/**
	 * Get the manifest to load assets from.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of files.
	 */
	private function getManifest() {
		static $file = null;
		if ( $file ) {
			return $file;
		}

		$manifestJson = ''; // This is set in the view.
		if ( file_exists( $this->manifestFile ) ) {
			require_once $this->manifestFile;
		}

		$file = json_decode( $manifestJson, true );

		return $file;
	}

	/**
	 * Get an item from the asset manifest.
	 *
	 * @since 1.0.0
	 *
	 * @param  string      $item An item to retrieve.
	 * @return string|null       The asset item.
	 */
	private function getAssetManifestItem( $item ) {
		$assetManifest = $this->getManifest();

		return ! empty( $assetManifest[ $item ] ) ? $assetManifest[ $item ] : null;
	}

	/**
	 * Get an asset's array of URLs to import.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find imports for.
	 * @return array         An array of imports.
	 */
	private function importsUrls( $asset ) {
		$urls          = [];
		$manifestAsset = $this->getManifestItem( $asset );
		if ( ! empty( $manifestAsset['imports'] ) ) {
			foreach ( $manifestAsset['imports'] as $import ) {
				$importAsset = $this->getManifestItem( $import );
				if ( ! empty( $importAsset['file'] ) ) {
					$urls[] = $this->getPublicUrlBase() . $importAsset['file'];

					// Load the import's CSS if any.
					$this->loadCss( $import );
				}
			}
		}

		return $urls;
	}

	/**
	 * Returns an asset's CSS urls.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $asset The asset to find CSS URLs for.
	 * @return array         An array of CSS URLs to load.
	 */
	private function getCssUrls( $asset ) {
		$urls          = [];
		$manifestAsset = $this->getManifestItem( $asset );

		if ( ! empty( $manifestAsset['css'] ) ) {
			foreach ( $manifestAsset['css'] as $file ) {
				$urls[ $file ] = $this->getPublicUrlBase() . $file;
			}
		}

		return $urls;
	}

	/**
	 * Check if we should load the dev watcher scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean True if we should load the dev watcher scripts.
	 */
	private function shouldLoadDev() {
		if ( null !== $this->shouldLoadDevScripts ) {
			return $this->shouldLoadDevScripts;
		}

		if (
			! $this->isDev ||
			(
				defined( 'AIOSEO_TABLE_OF_CONTENTS_LOAD_DEV_SCRIPTS' ) &&
				false === AIOSEO_TABLE_OF_CONTENTS_LOAD_DEV_SCRIPTS
			)
		) {
			$this->shouldLoadDevScripts = false;

			return $this->shouldLoadDevScripts;
		}

		if ( ! $this->domain && ! $this->port ) {
			$this->shouldLoadDevScripts = false;

			return $this->shouldLoadDevScripts;
		}

		set_error_handler( function() {} ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_set_error_handler
		$connection = fsockopen( $this->domain, $this->port ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		restore_error_handler();

		if ( ! $connection ) {
			$this->shouldLoadDevScripts = false;

			return $this->shouldLoadDevScripts;
		}

		$this->shouldLoadDevScripts = true;

		return $this->shouldLoadDevScripts;
	}

	/**
	 * Get the path for the assets.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $maybeDev Whether to try and load dev scripts.
	 * @return string           The path for the assets.
	 */
	public function getAssetsPath( $maybeDev = true ) {
		return $maybeDev && $this->shouldLoadDev()
			? $this->getDevUrl()
			: $this->basePath();
	}

	/**
	 * Finds out if a handle should be loaded as regular JS and not as modern JS module.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $handle The script handle.
	 * @return bool           Should the module tag be skipped.
	 */
	public function skipModuleTag( $handle ) {
		if ( ! aioseoTableOfContents()->helpers->stringContains( $handle, $this->jsHandle( '' ) ) ) {
			return true;
		}

		foreach ( $this->noModuleTag as $tag ) {
			if ( aioseoTableOfContents()->helpers->stringContains( $handle, $tag ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Normalize the assets host. Some sites manually set the WP_PLUGINS_URL
	 * and if that domain has www. and the site_url does not, then it will fail to load
	 * our assets. This doesn't fix the issue 100% because it will still fail on
	 * sub-domains that don't have the proper CORS headers. Those sites will need
	 * manual fixes.
	 *
	 * 4.1.10
	 *
	 * @param  string $path The path to normalize.
	 * @return string       The normalized path.
	 */
	public function normalizeAssetsHost( $path ) {
		static $paths = [];
		if ( isset( $paths[ $path ] ) ) {
			return apply_filters( 'aioseo_table_of_contents_normalize_assets_host', $paths[ $path ] );
		}

		// We need to verify the domain on the $path attribute matches
		// what's in site_url() for our assets or they won't load.
		$siteUrl        = site_url();
		$siteUrlEscaped = aioseoTableOfContents()->helpers->escapeRegex( $siteUrl );
		if ( preg_match( "/^$siteUrlEscaped/i", (string) $path ) ) {
			$paths[ $path ] = $path;

			return apply_filters( 'aioseo_table_of_contents_normalize_assets_host', $paths[ $path ] );
		}

		// We now know that the path doesn't contain the site_url().
		$newPath        = $path;
		$siteUrlParsed  = wp_parse_url( $siteUrl );
		$host           = aioseoTableOfContents()->helpers->escapeRegex( str_replace( 'www.', '', $siteUrlParsed['host'] ) );
		$scheme         = aioseoTableOfContents()->helpers->escapeRegex( $siteUrlParsed['scheme'] );

		$siteUrlHasWww = preg_match( "/^{$scheme}:\/\/www\.$host/", (string) $siteUrl );
		$pathHasWww    = preg_match( "/^{$scheme}:\/\/www\.$host/", (string) $path );

		// Check if the path contains www.
		if ( $pathHasWww && ! $siteUrlHasWww ) {
			// If the path contains www., we want to strip it out.
			$newPath = preg_replace( "/^({$scheme}:\/\/)(www\.)($host)/", '$1$3', (string) $path );
		}

		// Check if the site_url contains www.
		if ( $siteUrlHasWww && ! $pathHasWww ) {
			// If the site_url contains www., we want to add it in to the path.
			$newPath = preg_replace( "/^({$scheme}:\/\/)($host)/", '$1www.$2', (string) $path );
		}

		$paths[ $path ] = $newPath;

		return apply_filters( 'aioseo_table_of_contents_normalize_assets_host', $paths[ $path ] );
	}

	/**
	 * Returns the public URL base.
	 *
	 * @since 1.0.0
	 *
	 * @return string The URL base.
	 */
	private function getPublicUrlBase() {
		return $this->shouldLoadDev() ? $this->getDevUrl() . 'dist/assets/' : $this->basePath();
	}

	/**
	 * Returns the base path URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string The base path URL.
	 */
	private function basePath() {
		return $this->normalizeAssetsHost( plugins_url( 'dist/assets/', AIOSEO_TABLE_OF_CONTENTS_FILE ) );
	}
}