<?php


namespace ColibriWP\Theme;

use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Core\Utils;
use function wp_add_inline_script;
use function wp_add_inline_style;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_localize_script;

class AssetsManager {

	private $theme;
	private $key;

	private $fonts = array();

	private $autoenqueue
		= array(
			'style'  => array(),
			'script' => array(),
		);


	private $registered
		= array(
			'style'  => array(),
			'script' => array(),
		);

	private $localized = array();
	private $base_url  = '';
	private $base_dir  = '';
	private $is_hot    = false;

	/**
	 * AssetsManager constructor.
	 *
	 * @param Theme $theme
	 */
	public function __construct( $theme ) {
		$this->theme = $theme;
		$this->key   = Defaults::get( 'assets_js_key', 'THEME_DATA' );

		$this->base_url = Theme::rootDirectoryUri() . '/resources';
		$this->base_dir = Theme::rootDirectory() . '/resources';

	}

	public static function addInlineScriptCallback( $handle, $callback ) {
		wp_add_inline_script( $handle, Utils::buffer_wrap( $callback, true ) );
	}

	public static function addInlineStyleCallback( $handle, $callback ) {
		wp_add_inline_style( $handle, Utils::buffer_wrap( $callback, true ) );
	}

	public function boot() {
		add_action( 'wp_footer', array( $this, 'addFrontendJSData' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'doEnqueueGoogleFonts' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'doRegisterScript' ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'doRegisterScript' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'doAutoEnqueue' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'doLocalize' ), 40 );
	}

	public function addFrontendJSData() {
		$data   = Hooks::apply_filters( 'frontend_js_data', array() );
		$script = "window.{$this->key} = " . wp_json_encode( $data ) . ';';

		?>
		<script data-name="colibri-frontend-data"><?php echo $script; ?></script>
		<?php
	}

	public function doRegisterScript() {

		foreach ( $this->registered['style'] as $handle => $data ) {
			wp_register_style( $handle, $data['src'], $data['deps'], $data['ver'], $data['media'] );
		}

		foreach ( $this->registered['script'] as $handle => $data ) {
			wp_register_script( $handle, $data['src'], $data['deps'], $data['ver'], $data['in_footer'] );
		}
	}


	public function loadLocalGoogleFonts() {
		$fonts_url = $this->base_url . '/google-fonts/style.css';
		$this->registerStyle( $this->theme->getTextDomain() . '_local_google_fonts', $fonts_url );
	}

	public function doEnqueueGoogleFonts() {

		return;
		if ( Hooks::prefixed_apply_filters( 'skip_google_fonts', false ) ) {
			return;
		}

		$fontQuery = array();

		foreach ( $this->fonts as $family => $font ) {
			$fontQuery[] = $family . ':' . implode( ',', $font['weights'] );
		}

		$query_args = array(
			'family'  => urlencode( implode( '|', $fontQuery ) ),
			'subset'  => urlencode( 'latin,latin-ext' ),
			'display' => 'swap',
		);

		$fontsURL = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );

		$this->registerStyle( $this->theme->getTextDomain() . '_google_fonts', $fontsURL );
	}

	/**
	 * @param       $handle
	 * @param       $url
	 * @param array  $deps
	 * @param bool   $auto_enqueue
	 *
	 * @return AssetsManager
	 */
	public function registerStyle( $handle, $url, $deps = array(), $auto_enqueue = true ) {
		$this->register(
			'style',
			$handle,
			array(
				'src'          => $url,
				'deps'         => $deps,
				'auto_enqueue' => $auto_enqueue,
			)
		);

		return $this;
	}

	/**
	 * @param       $type
	 * @param       $handle
	 * @param array  $args
	 *
	 * @return AssetsManager
	 */
	public function register( $type, $handle, $args = array() ) {
		$ver  = $this->theme->getVersion();
		$data = array_merge(
			array(
				'src'          => '',
				'deps'         => array(),
				'has_min'      => false,
				'in_footer'    => true,
				'media'        => 'all',
				'ver'          => $ver,
				'in_preview'   => true,
				'auto_enqueue' => false,
			),
			$args
		);

		if ( $this->theme->getCustomizer()->isInPreview() && $data['in_preview'] === false ) {
			return $this;
		}

		if ( $data['has_min'] ) {
			if ( $type === 'style' ) {
				$data['src'] = Utils::replace_file_extension( $data['src'], '.css', '.min.css' );
			}

			if ( $type === 'script' ) {
				$data['src'] = Utils::replace_file_extension( $data['src'], '.js', '.min.js' );
			}
		}

		$this->registered[ $type ][ $handle ] = $data;

		if ( $data['auto_enqueue'] ) {
			if ( ! in_array( $handle, $this->autoenqueue[ $type ] ) ) {
				$this->autoenqueue[ $type ][] = $handle;
			}
		}

		return $this;
	}

	public function doAutoEnqueue() {

		foreach ( Hooks::prefixed_apply_filters( 'auto_enqueue_assets', $this->autoenqueue ) as $type => $content ) {
			foreach ( $content as $item ) {
				$this->enqueue( $type, $item );
			}
		}

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	public function enqueue( $type, $handle, $args = array() ) {
		if ( ! empty( $args ) ) {
			$this->register( $type, $handle, $args );
		}

		if ( $type === 'style' ) {
			wp_enqueue_style( $handle );
		}

		if ( $type === 'script' ) {

			if ( isset( $this->localized[ $handle ] ) ) {
				wp_localize_script( $handle, $this->localized[ $handle ]['key'], $this->localized[ $handle ]['data'] );
				unset( $this->localized[ $handle ] );
			}

			wp_enqueue_script( $handle );
		}
	}

	public function doLocalize() {
		foreach ( $this->localized as $handle => $data ) {
			wp_localize_script( $handle, $data['key'], $data['data'] );
		}
	}

	public function enqueueScript( $handle, $args = array() ) {
		$this->enqueue( 'script', $handle, $args );
	}

	public function enqueueStyle( $handle, $args = array() ) {
		$this->enqueue( 'style', $handle, $args );
	}

	/**
	 * @param string $handle
	 * @param string $rel
	 * @param array  $deps
	 * @param bool   $auto_enqueue
	 *
	 * @return AssetsManager
	 */
	public function registerTemplateScript( $handle, $rel, $deps = array(), $auto_enqueue = true ) {
		$this->registerScript( $handle, $this->getBaseURL() . $rel, $deps, $auto_enqueue );

		return $this;
	}

	/**
	 * @param string $handle
	 * @param string $rel
	 * @param array  $deps
	 * @param bool   $auto_enqueue
	 *
	 * @return AssetsManager
	 */
	public function registerScript( $handle, $url, $deps = array(), $auto_enqueue = true, $in_footer = true ) {

		$this->register(
			'script',
			$handle,
			array(
				'src'          => $url,
				'deps'         => $deps,
				'auto_enqueue' => $auto_enqueue,
				'in_footer'    => $in_footer,
			)
		);

		return $this;
	}

	public function getBaseURL() {
		return $this->base_url;
	}

	public function registerStylesheet( $handle, $hot_rel, $deps = array(), $auto_enqueue = true ) {
		if ( $this->is_hot ) {
			$this->registerTemplateStyle( $handle, "/{$hot_rel}", $deps, $auto_enqueue );
		} else {
			$this->registerStyle( $handle, get_stylesheet_uri(), $deps, $auto_enqueue );
		}

		return $this;
	}

	/**
	 * @param string $handle
	 * @param string $rel
	 * @param array  $deps
	 * @param bool   $auto_enqueue
	 *
	 * @return AssetsManager
	 */
	public function registerTemplateStyle( $handle, $rel, $deps = array(), $auto_enqueue = true ) {

		$this->registerStyle( $handle, $this->getBaseURL() . $rel, $deps, $auto_enqueue );

		return $this;
	}

	/**
	 * @param string $handle
	 * @param string $key
	 * @param array  $data
	 *
	 * @return AssetsManager
	 */
	public function localize( $handle, $key, $data = array() ) {
		$this->localized[ $handle ] = array(
			'key'  => $key,
			'data' => $data,
		);

		return $this;
	}

	public function addGoogleFont( $family, $weights ) {
		$this->fonts[ $family ] = compact( 'family', 'weights' );

		return $this;
	}

	public function clearGoogleFonts() {
		$this->fonts = array();

		return $this;
	}

	public function removeGoogleFont( $family, $weights = 'all' ) {

		if ( array_key_exists( $family, $this->fonts ) ) {
			if ( $weights === 'all' ) {
				unset( $this->fonts[ $family ] );

				return $this;
			} else {

				$weights = (array) $weights;

				foreach ( $weights as $weight ) {
					$font_weights = Utils::pathGet( $this->fonts, "{$family}.weights" );
					if ( array_key_exists( $weight, $font_weights ) ) {
						unset( $font_weights[ $weight ] );
					}

					if ( count( $font_weights ) ) {
						$this->fonts = Utils::pathSet( $this->fonts, "{$family}.weights", $font_weights );
					} else {
						unset( $this->fonts[ $family ] );
					}
				}

				return $this;
			}
		}

		return $this;
	}
}
